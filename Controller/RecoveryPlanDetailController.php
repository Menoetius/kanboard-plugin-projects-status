<?php
/**
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 19.2.19
 * Time: 20:08
 */

namespace Kanboard\Plugin\Status\Controller;
use Kanboard\Controller\BaseController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Security\Role;


/**
 * Class RecoveryPlanDetailController
 * @package Kanboard\Plugin\Status\Controller
 */
class RecoveryPlanDetailController extends BaseController
{
    /**
     * Shows main page of recovery plan
     *
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function index()
    {
        $project = $this->getProject();
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);
        $recovery_plan = $this->recoveryPlanModel->getRecoveryPlanById($recovery_plan_id);
        $creator = $this->userModel->getById($recovery_plan['owner_id']);
        $issues = $this->issueModel->getAllIssuesByRecoveryPlanId($recovery_plan_id);

        $issue_status_options = array(t('On hold'),t('Investigating'),t('Implementing'),t('Testing'), t('Done'));

        $this->response->html($this->helper->layout->app('status:recoveryPlanDetail/index', array(
            'title'       => $project['name'],
            'project'      => $project,
            'recovery_plan' => $recovery_plan,
            'issue_status_options' => $issue_status_options,
            'creator'   => $creator,
            'issues'    => $issues
        )));
    }

    /**
     * Confirmation dialog before deleting recovery plan
     *
     * @access public
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function removeConfirm()
    {
        $project = $this->getProject();
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);


        $this->response->html($this->template->render('status:recoveryPlanDetail/remove', array(
            'title'         => t('Remove recovery plan'),
            'project_id'       => $project['id'],
            'values' => array('id' => $recovery_plan_id)
        )));
    }

    /**
     * Removes recovery plan
     *
     * @access public
     */
    public function remove()
    {
        $values = $this->request->getValues();

        if ($this->recoveryPlanModel->remove($values['id'])) {
            $this->flash->success(t('Recovery plan deleted successfully.'));
        } else {
            $this->flash->failure(t('Unable to delete this recovery plan.'));
        }

        $this->response->redirect($this->helper->url->to('StatusController', 'index', array('plugin' => 'Status')));
    }


    /**
     * Confirmation dialog before marking a recovery plan inactive
     *
     * @access public
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function deactivate()
    {
        $project = $this->getProject();
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);


        $this->response->html($this->template->render('status:recoveryPlanDetail/makeInactive', array(
            'title'         => t('Remove recovery plan'),
            'project_id'       => $project['id'],
            'values' => array('id' => $recovery_plan_id)
        )));
    }

    /**
     * Inactivates recovery plan
     *
     * @access public
     */
    public function makeInactive()
    {
        $values = $this->request->getValues();

        if ($this->recoveryPlanModel->deactivate($values['id'])) {
            $this->flash->success(t('Recovery plan passivated successfully.'));
        } else {
            $this->flash->failure(t('Unable to passivate this recovery plan.'));
        }

        $this->response->redirect($this->helper->url->to('StatusController', 'index', array('plugin' => 'Status')));
    }

    /**
     * Confirmation dialog before reactivating a recovery plan
     *
     * @access public
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function reactivate()
    {
        $project = $this->getProject();
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);


        $this->response->html($this->template->render('status:recoveryPlanDetail/makeActive', array(
            'title'         => t('Reactivate recovery plan'),
            'project_id'       => $project['id'],
            'values' => array('id' => $recovery_plan_id)
        )));
    }

    /**
     * Reactivates recovery plan
     *
     * @access public
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function makeActive()
    {
        $values = $this->request->getValues();
        $project = $this->getProject();

        if ($this->recoveryPlanModel->reactivate($values['id'], $project['id'])) {
            $this->flash->success(t('Recovery plan activated successfully.'));
            $this->response->redirect($this->helper->url->to('RecoveryPlanDetailController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'], 'recovery_plan_id' => $values['id'])));
        } else {
            $this->flash->failure(t('Unable to activate this recovery plan.'));
            $this->response->redirect($this->helper->url->to('StatusController', 'index', array('plugin' => 'Status')));
        }
    }

    /**
     * @throws AccessForbiddenException
     */
    public function saveStatus() {
        $project_id = $this->request->getIntegerParam('project_id');
        $values = $this->request->getJson();

        if (!empty($values) && $this->helper->user->hasProjectAccess('RecoveryPlanDetailController', 'saveStatus', $project_id)) {
            $result = $this->issueModel->changeStatus($values['issue_id'], $values['status']);
            $this->response->json(array('result' => $result));
        } else {
            throw new AccessForbiddenException(t('You are not allowed to change issue status'));
        }
    }
}