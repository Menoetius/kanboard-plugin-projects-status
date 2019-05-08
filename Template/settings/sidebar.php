<li <?= $this->app->checkMenuSelection('SettingsController', 'index', 'Status') ?>>
    <?= $this->url->link(t('Project status settings'), 'SettingsController', 'index', array('plugin' => 'Status')) ?>
</li>