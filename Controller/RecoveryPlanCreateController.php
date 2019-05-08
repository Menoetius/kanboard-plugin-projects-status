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
 * Class RecoveryPlanCreateController
 * @package Kanboard\Plugin\Status\Controller
 */
class RecoveryPlanCreateController extends BaseController
{
    /**
     * Shows recovery plan creation form
     *
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function index($values = array(), array $errors = array())
    {

        $project = $this->getProject();

        $this->response->html($this->template->render('status:recoveryPlanCreate/index', array(
            'title' => $project['name'] . ': ' . 'New recovery plan',
            'project' => $project,
            'values' => $values,
            'errors' => $errors
        )));
    }

    /**
     * Shows recovery plan modification form
     *
     * @param array $errors
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function edit(array $errors = array())
    {
        $project = $this->getProject();
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);
        $values = $this->recoveryPlanModel->getRecoveryPlanById($recovery_plan_id);

        $this->response->html($this->template->render('status:recoveryPlanCreate/index', array(
            'title' => $project['name'] . ': ' . t('editing recovery plan'),
            'project' => $project,
            'values' => array('id' => $values['id'], 'accomplished' => $values['accomplished'], 'plan' => $values['plan']),
            'errors' => $errors
        )));
    }

    /**
     * Validates and saves a recovery plan
     *
     * @access public
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function save()
    {
        $values = $this->request->getValues();
        $project = $this->getProject();

        if (!$values['id']) {
            $values['date'] = time();
            $values['owner_id'] = $this->userSession->getId();
            $values['project_id'] = $project['id'];
        } else {
            $values['user_modified'] = $this->userSession->getId();
            $values['last_modified'] = time();
        }

        list($valid, $errors) =  $this->helper->recoveryPlanValidator->validateCreation($values);

        if ($valid) {

            $recovery_plan_id = $this->onBeforeStore($values);

            if ($recovery_plan_id > 0) {
                $this->flash->success(t('Your recovery plan have been created successfully.'));
                return $this->response->redirect($this->helper->url->to('RecoveryPlanDetailController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'], 'recovery_plan_id' => $recovery_plan_id)));
            }

            $this->flash->failure(t('Unable to create your recovery plan.'));
        }

        if ($values['id']) {
            return $this->edit($values, $errors);
        } else {
            return $this->index($values, $errors);
        }
    }


    /**
     * Saves recovery plan
     *
     * @access private
     * @param array $values
     * @return boolean|integer
     */
    private function onBeforeStore(array $values)
    {
        if ($values['id']) {
            $plan = array(
                'id' => $values['id'],
                'accomplished' => $values['accomplished'],
                'plan' => $values['plan'],
                'last_modified' => $values['last_modified'],
                'user_modified' => $values['user_modified'],
            );

            return $this->recoveryPlanModel->update($plan);
        } else {
            $plan = array(
                'accomplished' => $values['accomplished'],
                'plan' => $values['plan'],
                'project_id' => $values['project_id'],
                'owner_id' => $values['owner_id'],
                'date' => $values['date'],
                'last_modified' => $values['last_modified'],
                'user_modified' => $values['user_modified'],
                'is_active' => $values['is_active']
            );

            return $this->recoveryPlanModel->create($plan);
        }
    }
}