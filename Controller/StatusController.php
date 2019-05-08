<?php
/**
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 15.1.19
 * Time: 21:30
 */

namespace Kanboard\Plugin\Status\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Controller\AccessForbiddenException;


/**
 * Class StatusController
 * @package Kanboard\Plugin\Status\Controller
 */
class StatusController extends BaseController
{
    /**
     *  Constant of project quick status for search
     *
     */
    const STATUS = ['green' => 0, 'amber' => 1, 'red' => 2];

    /**
     * Shows Status Plugin main dashboard
     * Page can be modified thought plugin settings
     *
     */
    public function index()
    {
        if ($this->userSession->isAdmin()) {
            $projectIds = $this->projectModel->getAllIds();
        } else {
            $projectIds = $this->projectPermissionModel->getProjectIds($this->userSession->getId());
        }

        $paginator_limit = $this->configModel->get('status_project_pagination', 2);
        $critical_tasks = $this->configModel->get('critical_tasks', 'critical_tasks') === 'critical_tasks';
        $projects_graphs = $this->configModel->get('projects_graphs', 'projects_graphs') === 'projects_graphs';
        $project_description = $this->configModel->get('project_description', 'project_description') === 'project_description';

        $query = $this->statusModel->getQueryByProjectIdsWithOwnerAvatar($projectIds);
        $search = $this->request->getStringParam('search');

        if ($search !== '' && !strpos($search, ':')) {
            $query->ilike('projects.name', '%' . $search . '%');
        } else if ($search !== '') {
            preg_match_all('/([a-z]+:)(".+?"|\w+?)(\s|$)/', $search, $tokens, PREG_SET_ORDER);
            if (count($tokens)) {
                $filterStatus = $filterProjects = $filterOwners = array();
                foreach ($tokens as $token) {
                    if ($token[1] === "status:") {
                        array_push($filterStatus, $token[2]);
                    } elseif ($token[1] === "project:") {
                        array_push($filterProjects, $token[2]);
                    } elseif ($token[1] === "owner:") {
                        array_push($filterOwners, $token[2]);
                    }
                }

                if (count($filterStatus)) {
                    if (count($filterStatus) > 1) {
                        $query->beginOr();
                    }
                    foreach ($filterStatus as $searchedValue) {
                        $query->eq('projects.project_status', self::STATUS[$searchedValue]);
                    }
                    if (count($filterStatus) > 1) {
                        $query->closeOr();
                    }
                }

                if (count($filterProjects)) {
                    if (count($filterProjects) > 1) {
                        $query->beginOr();
                    }
                    foreach ($filterProjects as $searchedValue) {
                        if ($searchedValue[0] === '"') {
                            $query->ilike('projects.name', substr($searchedValue, 1, -1));
                        } else {
                            $query->eq('projects.id', $searchedValue);
                        }
                    }
                    if (count($filterProjects) > 1) {
                        $query->closeOr();
                    }
                }

                if (count($filterOwners)) {
                    if (count($filterOwners) > 1) {
                        $query->beginOr();
                    }
                    foreach ($filterOwners as $searchedValue) {
                        if ($searchedValue === 'me') {
                            $query->ilike('users.username', '%' . $this->userSession->getUsername() . '%');
                        } else if ($searchedValue[0] === '"') {
                            $query->ilike('users.name', substr($searchedValue, 1, -1));
                        } else {
                            $query->ilike('users.username', '%' . $searchedValue . '%');
                        }
                    }
                    if (count($filterOwners) > 1) {
                        $query->closeOr();
                    }
                }
            } else {
                $query->eq('projects.id', 0);
            }
        }

        $paginator = $this->paginator
            ->setUrl('StatusController', 'index', array('plugin' => 'Status', 'search' => $search))
            ->setMax($paginator_limit)
            ->setOrder('name')
            ->setQuery($query)
            ->calculate();

        $tasks = $recoveryPlans = $inActiveRecoveryPlans = $userDistributionGraphs = $userDistributionGraphsJSON = $taskDistributionGraphs = [];
        foreach ($projectIds as $projectId) {
            $tasks[$projectId] = $critical_tasks ? $this->statusModel->getHighlightedTasks($projectId) : false;
            $recoveryPlans[$projectId] = $this->statusModel->getLastRecoveryPlanOfProject($projectId);
            $inActiveRecoveryPlans[$projectId] = $this->statusModel->getLastRecoveryPlanOfProject($projectId, 0);
            $userDistributionGraphs[$projectId] = !$projects_graphs ?: $this->getUserGraphData($projectId);
            $taskDistributionGraphs[$projectId] = !$projects_graphs ?: $this->getTaskGraphData($projectId);
        }

        $this->response->html($this->helper->layout->app('status:dashboard/index', array(
            'paginator' => $paginator,
            'title' => t('Projects') . ' (' . $paginator->getTotal() . ')',
            'values' => array('search' => $search),
            'tasks' => $tasks,
            'userDistributionGraphs' => $userDistributionGraphs,
            'taskDistributionGraphs' => $taskDistributionGraphs,
            'show_graphs' => $projects_graphs,
            'show_project_description' => $project_description,
            'recoveryPlans' => $recoveryPlans,
            'inActiveRecoveryPlans' => $inActiveRecoveryPlans,
        )));
    }

    /**
     * Saves quick project status
     *
     */
    public
    function saveStatus()
    {
        $values = $this->request->getJson();

        if (!empty($values) && $this->helper->user->hasProjectAccess('StatusController', 'saveStatus', $values['project_id'])) {
            $result = $this->statusModel->changeProjectStatus($values['project_id'], $values['status']);
            $this->response->json(array('result' => $result));
        } else {
            throw new AccessForbiddenException();
        }
    }

    /**
     * Collects and prepares data for user graph
     *
     * @param $project_id
     * @return array
     */
    public
    function getUserGraphData($project_id)
    {
        $metrics = array();
        $total = 0;
        $tasks = $this->statusModel->getAllTasksBy($project_id, '0');
        $users = $this->projectUserRoleModel->getAssignableUsersList($project_id);

        foreach ($tasks as $task) {
            $user = isset($users[$task['owner_id']]) ? $users[$task['owner_id']] : $users[0];
            $total++;

            if (!isset($metrics[$user])) {
                $metrics[$user] = array(
                    'tasks' => 0,
                    'closed' => 0,
                    'user' => $user,
                );
            }

            if ($task['is_active']) {
                $metrics[$user]['tasks']++;
            } else {
                $metrics[$user]['closed']++;
            }
        }

        if ($total === 0) {
            return array();
        }

        ksort($metrics);
        array_values($metrics);

        $users = $tasks = $closed = [];
        foreach ($metrics as $key => $value) {
            $users[] = $value['user'];
            $tasks[] = $value['tasks'];
            $closed[] = $value['closed'];
        }

        return array(
            'users' => $users,
            'tasks' => $tasks,
            'closed_tasks' => $closed
        );
    }

    /**
     * Collects and prepares data for task in columns graph
     *
     * @param $project_id
     * @return array
     */
    public
    function getTaskGraphData($project_id)
    {
        $metrics = array();
        $total = 0;
        $columns = $this->columnModel->getAll($project_id);

        foreach ($columns as $column) {
            $nb_tasks = $this->taskFinderModel->countByColumnId($project_id, $column['id']);
            $total += $nb_tasks;

            $metrics[] = array(
                'column_title' => $column['title'],
                'tasks' => $nb_tasks,
            );
        }

        if ($total === 0) {
            return array();
        }

        foreach ($metrics as &$metric) {
            $metric['percentage'] = round(($metric['tasks'] * 100) / $total, 2);
        }

        $column_title = $tasks = $percentage = [];
        foreach ($metrics as $key => $value) {
            $column_title[] = $value['column_title'];
            $tasks[] = $value['tasks'];
            $percentage[] = $value['percentage'];
        }

        return array(
            'column_title' => $column_title,
            'tasks' => $tasks,
            'percentage' => $percentage
        );
    }
}