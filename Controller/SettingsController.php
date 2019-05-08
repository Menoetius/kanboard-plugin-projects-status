<?php

namespace Kanboard\Plugin\Status\Controller;

/**
 * Class SettingsController
 *
 * @package Kanboard\Plugin\Status\Controller
 */
class SettingsController extends \Kanboard\Controller\ConfigController
{
    /**
     * Shows Status plugin settings page
     *
     * @param array $values
     */
    public function index($values = array())
    {
        $values['status_project_dashboard'] = array($this->configModel->get('critical_tasks', 'critical_tasks'), $this->configModel->get('projects_graphs', 'projects_graphs'), $this->configModel->get('project_description', 'project_description'));
        $values['status_project_pagination'] = $this->configModel->get('status_project_pagination', 2);
        $this->response->html($this->helper->layout->config('Status:settings/status', array(
            'title' => t('Settings') . ' &gt; ' . t('Project status settings'),
            'values' => $values
        )));
    }

    /**
     * Saves settings
     *
     */
    public function save()
    {
        $values = $this->request->getValues();

        $values['critical_tasks'] = isset($values['status_project_dashboard']["critical_tasks"]) && in_array("critical_tasks", $values['status_project_dashboard'], true) ? "critical_tasks" : 'false';
        $values['projects_graphs'] = isset($values['status_project_dashboard']["projects_graphs"]) && in_array("projects_graphs", $values['status_project_dashboard'], true) ? "projects_graphs" : 'false';
        $values['project_description'] = isset($values['status_project_dashboard']["project_description"]) && in_array("project_description", $values['status_project_dashboard'], true) ? "project_description" : 'false';

        if ($this->configModel->save($values)) {
            $this->flash->success(t('Settings have been saved successfully.'));
        } else {
            $this->flash->failure(t('Unable to save your settings.'));
        }

        $this->response->redirect($this->helper->url->to('SettingsController', 'index', array('plugin' => 'Status')));
    }
}
