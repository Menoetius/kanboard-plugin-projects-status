<?php
/**
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 19.2.19
 */

namespace Kanboard\Plugin\Status\Controller;
use Kanboard\Controller\BaseController;


/**
 * Class RecoveryPlanDetailController
 * @package Kanboard\Plugin\Status\Controller
 */
class ProjectsReportController extends BaseController
{
    /**
     * Shows report of all projects in table with option to export it
     *
     */
    public function index()
    {
        if ($this->userSession->isAdmin()) {
            $projectIds = $this->projectModel->getAllIds();
        } else {
            $projectIds = $this->projectPermissionModel->getProjectIds($this->userSession->getId());
        }

        $data = $this->projectsReportModel->getTableData($projectIds);

        $issues = array();
        foreach($data as $row) {
            $issues[$row['id']] = $this->issueModel->getAllIssuesByRecoveryPlanId($row['id']);
        }

        $this->response->html($this->helper->layout->app('status:projectsReport/index', array(
            'title'       => t('Projects report'),
            'data' => $data,
            'issues' => $issues
        )));
    }
}