<?php
/**
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 15.1.19
 */

namespace Kanboard\Plugin\Status\Controller;

use Kanboard\Controller\BaseController;


/**
 * Class IssueStepsController
 * @package Kanboard\Plugin\Status\Controller
 */
class IssueStepsController extends BaseController
{

    /**
     * Shows page of issue commentaries and solution design
     *
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function index()
    {

        $project = $this->getProject();

        $issue_id = $this->request->getIntegerParam('issue_id', 0);
        $issue = $this->issueModel->getIssueById($issue_id);
        $assignee = $this->userModel->getById($issue['user_assignee']);
        $steps = $this->issueStepsModel->getAllStepsByIssueId($issue_id);

        $this->response->html($this->helper->layout->app('status:issueSteps/index', array(
            'title' => '#' . $issue['id'] . ' ' . $issue['name'],
            'project' => $project,
            'assignee' => $assignee,
            'issue' => $issue,
            'steps' => $steps
        )));
    }

    /**
     * Shows commentary modification dialog
     *
     * @param array $errors
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function edit(array $errors = array())
    {

        $project = $this->getProject();
        $issue_id = $this->request->getIntegerParam('issue_id', 0);
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);
        $issueStepId = $this->request->getIntegerParam('issue_step_id', 0);

        $values = $this->issueStepsModel->getIssueStepsById($issueStepId);

        $this->response->html($this->template->render('status:issueSteps/edit', array(
            'title' => t('Edit issue commentary'),
            'project' => $project,
            'issue_id' => $issue_id,
            'recovery_plan_id' => $recovery_plan_id,
            'values' => $values,
            'errors' => $errors
        )));
    }

    /**
     * Prepares commentary for save
     *
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function save()
    {
        $values = $this->request->getValues();
        $project = $this->getProject();
        $recovery_plan_id = $this->request->getIntegerParam('recovery_plan_id', 0);
        $issue_id = $this->request->getIntegerParam('issue_id', 0);

        if (!$values['id']) {
            $values['issue_id'] = $issue_id;
            $values['recovery_plan_id'] = $recovery_plan_id;
            $values['date_creation'] = time();
            $values['owner_id'] = $this->userSession->getId();
        }

        $issue_step_id = $this->onBeforeStore($values);

        if ($issue_step_id > 0) {
            $this->flash->success(t('Comment submitted.'));
            $this->recoveryPlanModel->update(array('id' => $recovery_plan_id, 'last_modified' => time(), 'user_modified' => $this->userSession->getId()));
            return $this->response->redirect($this->helper->url->to('IssueStepsController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'], 'issue_id' => $issue_id)));
        }

        $this->flash->failure(t('Unable to submit comment.'));

        if ($values['id']) {
            return $this->edit($values);
        } else {
            return $this->index();
        }
    }


    /**
     * Saves commentary
     *
     * @access private
     * @param  array $values
     * @return boolean|integer
     */
    private function onBeforeStore(array $values)
    {
        if (!$values['id']) {
            $steps = array(
                'recovery_plan_id' => $values['recovery_plan_id'],
                'date_creation' => $values['date_creation'],
                'owner_id' => $values['owner_id'],
                'issue_id' => $values['issue_id'],
                'text' => $values['text']
            );
        } else {
            $steps = array(
                'id' => $values['id'],
                'text' => $values['text']
            );
        }

        return $this->issueStepsModel->createIssueSteps($steps);
    }

    /**
     * Confirmation dialog before removing a commentary
     *
     * @access public
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function confirm()
    {
        $project = $this->getProject();
        $issue_id = $this->request->getIntegerParam('issue_id', 0);
        $issueStepId = $this->request->getIntegerParam('issue_step_id', 0);

        $issueStep = $this->issueStepsModel->getIssueStepsById($issueStepId);

        $this->response->html($this->template->render('status:issueSteps/delete', array(
            'title' => t('Remove commentary'),
            'project_id' => $project['id'],
            'issue_id' => $issue_id,
            'values' => array('id' => $issueStepId),
            'issueStep' => $issueStep
        )));
    }

    /**
     * Removes commentary
     *
     * @access public
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function remove()
    {
        $values = $this->request->getValues();
        $project = $this->getProject();
        $issue_id = $this->request->getIntegerParam('issue_id', 0);


        if ($this->issueStepsModel->remove($values['id'])) {
            $this->flash->success(t('Commentary removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this commentary.'));
        }

        $this->response->redirect($this->helper->url->to('IssueStepsController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'], 'issue_id' => $issue_id)));
    }

}