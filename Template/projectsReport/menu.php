<div class="page-header">
    <ul>
        <li class="active">
            <?= $this->url->icon('bar-chart', t('Projects status'), 'StatusController', 'index', ['plugin' => 'Status', ]) ?>
        </li>

        <?php if ($this->user->hasAccess('ProjectCreationController', 'create')): ?>
            <li>
                <?= $this->modal->medium('plus', t('New project'), 'ProjectCreationController', 'create') ?>
            </li>
        <?php endif ?>

        <?php if ($this->app->config('disable_private_project', 0) == 0): ?>
            <li>
                <?= $this->modal->medium('lock', t('New private project'), 'ProjectCreationController', 'createPrivate') ?>
            </li>
        <?php endif ?>

        <li>
            <?= $this->url->icon('folder', t('Project management'), 'ProjectListController', 'show') ?>
        </li>

        <?php if ($this->user->hasAccess('ProjectUserOverviewController', 'managers')): ?>
            <li><?= $this->url->icon('user', t('Users overview'), 'ProjectUserOverviewController', 'managers') ?></li>
        <?php endif ?>
    </ul>
</div>