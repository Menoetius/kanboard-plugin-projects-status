<?php
/**
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 19.2.19
 * Time: 20:08
 */

namespace Kanboard\Plugin\Status\Controller;
use Kanboard\Controller\BaseController;


/**
 * Class RecoveryPlanListController
 * @package Kanboard\Plugin\Status\Controller
 */
class RecoveryPlanListController extends BaseController
{
    /**
     * Shows list of recovery plan for project
     *
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function index()
    {
        $project = $this->getProject();
        $recovery_plans = $this->recoveryPlanModel->getRecoveryPlansByProjectId($project['id']);
        $query = $this->recoveryPlanModel->getQueryByRecoveryPlanIds($recovery_plans);

        $paginator = $this->paginator
            ->setUrl('RecoveryPlanListController', 'index', array('plugin' => 'Status', 'project_id' => $project['id']))
            ->setMax(20)
            ->setOrder('date')
            ->setQuery($query)
            ->calculate();

        $this->response->html($this->helper->layout->app('status:recoveryPlanList/index', array(
            'title'       => $project['name'],
            'project'      => $project,
            'paginator' => $paginator,
            'recovery_plans'      => $recovery_plans
        )));
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


        $this->response->html($this->template->render('status:recoveryPlanList/makeInactive', array(
            'title'         => t('Remove recovery plan'),
            'project_id'       => $project['id'],
            'values' => array('id' => $recovery_plan_id)
        )));
    }

    /**
     * Inactivates recovery plan
     *
     * @access public
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function makeInactive()
    {
        $values = $this->request->getValues();
        $project = $this->getProject();

        if ($this->recoveryPlanModel->deactivate($values['id'])) {
            $this->flash->success(t('Recovery plan passivated successfully.'));
        } else {
            $this->flash->failure(t('Unable to passivate this recovery plan.'));
        }

        $this->response->redirect($this->helper->url->to('RecoveryPlanListController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'])));
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


        $this->response->html($this->template->render('status:recoveryPlanList/makeActive', array(
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
        } else {
            $this->flash->failure(t('Unable to activate this recovery plan.'));
        }

        $this->response->redirect($this->helper->url->to('RecoveryPlanListController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'])));
    }
}