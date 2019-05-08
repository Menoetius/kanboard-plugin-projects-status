<?php
$routerController = $this->app->getRouterController();
$routerPlugin = $this->app->getPluginName();
$active = $routerController == 'StatusController' && $routerPlugin == 'Status';
?>
<li class="<?= $active ? 'active' : '' ?>">
    <?= $this->url->icon('bar-chart', t('Projects status'), 'StatusController', 'index', ['plugin' => 'Status', ]) ?>
</li>