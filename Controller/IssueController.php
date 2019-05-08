<?php
/**
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 15.1.19
 */

namespace Kanboard\Plugin\Status\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Controller\AccessForbiddenException;


/**
 * Class IssueController
 * @package Kanboard\Plugin\Status\Controller
 */
class IssueController extends BaseController
{

    /**
     * Shows issue creation form
     *
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function index($values = array(), array $errors = array())
    {

        $project = $this->getProject();
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);
        $recovery_plan = $this->recoveryPlanModel->getRecoveryPlanById($recovery_plan_id);
        $tasks = $this->taskFinderModel->getAll($project['id']);

        $this->response->html($this->template->render('status:issue/index', array(
            'title' => 'New issue',
            'project' => $project,
            'recovery_plan' => $recovery_plan,
            'tasks' => $this->prepareList($tasks),
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($project['id'], true, false, $project['is_private'] == 1),
            'values' => $values,
            'errors' => $errors
        )));
    }

    /**
     * Shows issue modification form
     *
     * @param array $errors
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function edit($values = array(), array $errors = array())
    {

        $project = $this->getProject();
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);
        $recovery_plan = $this->recoveryPlanModel->getRecoveryPlanById($recovery_plan_id);
        $tasks = $this->taskFinderModel->getAll($project['id']);

        $issue_id = $this->request->getIntegerParam('issue_id', 0);
        if (!count($values)) {
            $values = $this->issueModel->getIssueById($issue_id);
        }

        $this->response->html($this->template->render('status:issue/index', array(
            'title' => t('Edit issue'),
            'project' => $project,
            'recovery_plan' => $recovery_plan,
            'tasks' => $this->prepareList($tasks),
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($project['id'], true, false, $project['is_private'] == 1),
            'values' => $values,
            'errors' => $errors
        )));
    }

    /**
     * Public method to prepare and validate values of both creation and modification form
     *
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function save()
    {
        $values = $this->request->getValues();
        $project = $this->getProject();
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);

        $values['project_id'] = (int) $project['id'];
        $values['recovery_plan_id'] = (int) $recovery_plan_id;
        $values['date_creation'] = time();

        if ($values['id']) {
            list($valid, $errors) = $this->helper->issueValidator->validateModification($values);
        } else {
            list($valid, $errors) = $this->helper->issueValidator->validateCreation($values);
        }

        if ($valid) {
            $issue_id = $this->onBeforeStore($values);

            if ($issue_id > 0) {
                $this->flash->success(t('Your issue have been created successfully.'));
                $this->recoveryPlanModel->update(array('id' => $recovery_plan_id, 'last_modified' => time(), 'user_modified' => $this->userSession->getId()));
                return $this->response->redirect($this->helper->url->to('RecoveryPlanDetailController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'], 'recovery_plan_id' => $recovery_plan_id)));
            }

            $this->flash->failure(t('Unable to create your issue.'));
        }

        if ($values['id']) {
            return $this->edit($values, $errors);
        } else {
            return $this->index($values, $errors);
        }
    }


    /**
     * Saves issue
     *
     * @access private
     * @param array $values
     * @return boolean|integer
     */
    private function onBeforeStore(array $values)
    {
        $issue = array(
            'id' => ($values['id'] ? $values['id'] : null),
            'project_id' => $values['project_id'],
            'recovery_plan_id' => $values['recovery_plan_id'],
            'date_creation' => $values['date_creation'],
            'name' => $values['name'],
            'user_assignee' => $values['user_assignee'],
            'user_issued' => $values['user_issued'],
            'task_id' => $values['task_id'],
            'priority' => $values['priority'],
            'due_date' => $values['due_date'],
            'description' => $values['description']
        );

        return $this->issueModel->createIssue($issue);

    }


    /**
     * Move issue position
     *
     * @access public
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     */
    public function movePosition()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id');
        $values = $this->request->getJson();

        if (!empty($values) && $this->helper->user->hasProjectAccess('IssueController', 'movePosition', $project_id)) {
            $result = $this->issueModel->changePosition($recovery_plan_id, $values['issue_id'], $values['position']);
            $this->response->json(array('result' => $result));
            $this->recoveryPlanModel->update(array('id' => $recovery_plan_id, 'last_modified' => time(), 'user_modified' => $this->userSession->getId()));
        } else {
            throw new AccessForbiddenException(t('You are not allowed to move issues'));
        }
    }

    /**
     * Confirmation dialog before removing an issue
     *
     * @access public
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function confirm()
    {
        $project = $this->getProject();
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);

        $issue_id = $this->request->getIntegerParam('issue_id', 0);
        $issue = $this->issueModel->getIssueById($issue_id);

        $this->response->html($this->template->render('status:issue/delete', array(
            'title' => t('Remove issue'),
            'project_id' => $project['id'],
            'recovery_plan_id' => $recovery_plan_id,
            'values' => array('id' => $issue_id),
            'issue' => $issue
        )));
    }

    /**
     * Removes an issue
     *
     * @access public
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function remove()
    {
        $values = $this->request->getValues();
        $project = $this->getProject();
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);


        if ($this->issueModel->remove($values['id'])) {
            $this->flash->success(t('Issue removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this issue.'));
        }

        $this->response->redirect($this->helper->url->to('RecoveryPlanDetailController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'], 'recovery_plan_id' => $recovery_plan_id)));
    }


    /**
     * Private function prepares data for selection input of referenced task
     *
     * @param array $objects
     * @param bool $unreferenced
     * @return array
     */
    private function prepareList(array $objects, $unreferenced = true)
    {
        $result = array();

        foreach ($objects as $object) {
            $result[$object['id']] = $object['title'];
        }

        asort($result);

        if ($unreferenced) {
            $result = array(t('Unreferenced')) + $result;
        }

        return $result;
    }
}