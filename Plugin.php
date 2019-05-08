<?php

namespace Kanboard\Plugin\Status;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;
use Kanboard\Core\Security\Role;

class Plugin extends Base
{
    public function initialize()
    {
        $this->template->hook->attach('template:project-list:menu:before', 'status:main/menu');

        $this->helper->register('issue', '\Kanboard\Plugin\Status\Helper\IssueHelper');
        $this->helper->register('issueValidator', '\Kanboard\Plugin\Status\Validator\IssueValidator');
        $this->helper->register('recoveryPlanValidator', '\Kanboard\Plugin\Status\Validator\RecoveryPlanValidator');
        $this->hook->on('template:layout:js', array('template' => 'plugins/Status/Asset/js/IssuesDragAndDrop.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/Status/Asset/js/export.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/Status/Asset/js/jtoggler.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/Status/Asset/js/main.js'));
        $this->hook->on('template:layout:css', array('template' => 'plugins/Status/Asset/css/jtoggler.styles.css'));
        $this->hook->on('template:layout:css', array('template' => 'plugins/Status/Asset/css/main.styles.css'));
        $this->template->hook->attach('template:config:sidebar', 'Status:settings/sidebar');
        $this->projectAccessMap->add('IssueController', '*', Role::PROJECT_MANAGER);
        $this->projectAccessMap->add('RecoveryPlanCreateController', '*', Role::PROJECT_MANAGER);
        $this->projectAccessMap->add('RecoveryPlanDetailController', ['deactivate', 'reactivate'], Role::PROJECT_MANAGER);
        $this->projectAccessMap->add('RecoveryPlanListController', ['deactivate', 'reactivate'], Role::PROJECT_MANAGER);
    }

    public function getClasses()
    {
        return array(
            'Plugin\Status\Model' => array(
                'StatusModel',
                'RecoveryPlanModel',
                'IssueModel',
                'IssueStepsModel',
                'ProjectsReportModel'
            ),
            'Plugin\Status\Controller' => array(
                'IssueController',
                'IssueStepsController',
                'RecoveryPlanCreateController',
                'RecoveryPlanDetailController',
                'RecoveryPlanListController',
                'StatusController',
                'SettingsController',
                'ProjectsReportController'
            )
        );
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getPluginName()
    {
        return 'Project Status';
    }

    public function getPluginDescription()
    {
        return t('Project reviews, issue management and recovery plans for Kanboard');
    }

    public function getPluginAuthor()
    {
        return 'Andrej Masar';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/Menoetius/kanboard-plugin-projects-status';
    }
}

