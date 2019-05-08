<div class="projectsReportButton">
    <?= $this->url->link(t('Summary report'), 'ProjectsReportController', 'index', array('plugin' => 'Status')) ?>
</div>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is no project.') ?></p>
<?php else: ?>
    <div class="table-list switch-wrapper"
         data-save-position-url="<?= $this->url->href('StatusController', 'saveStatus', array('plugin' => 'Status')) ?>">
        <?php foreach ($paginator->getCollection() as $project): ?>
            <div class="project-row margin-bottom">
                <div class="table-list-header flex-row">

                    <strong class="table-list-title <?= $project['is_active'] == 0 ? 'status-closed' : '' ?>">
                        <?= $this->url->link($this->text->e('#' . $project['id'] . ' ' . $project['name']), 'BoardViewController', 'show', array('project_id' => $project['id'])) ?>
                    </strong>

                    <div class="table-list-icons">
                        <?php if ($project['is_public']): ?>
                            <i class="fa fa-share-alt fa-fw" title="<?= t('Shared project') ?>"></i>
                        <?php endif ?>

                        <?php if ($project['is_private']): ?>
                            <i class="fa fa-lock fa-fw" title="<?= t('Private project') ?>"></i>
                        <?php endif ?>

                        <?php if ($this->user->hasAccess('ProjectUserOverviewController', 'managers')): ?>
                            <?= $this->app->tooltipLink('<i class="fa fa-users"></i>', $this->url->href('ProjectUserOverviewController', 'users', array('project_id' => $project['id']))) ?>
                        <?php endif ?>

                        <?php if (!empty($project['description'])): ?>
                            <?= $this->app->tooltipMarkdown($project['description']) ?>
                        <?php endif ?>

                        <?php if ($project['is_active'] == 0): ?>
                        <i class="fa fa-ban fa-fw" aria-hidden="true" title="<?= t('Closed') ?>"></i><?= t('Closed') ?>
                        <?php endif ?>
                    </div>

                </div>

                <div class="table-list-row table-border-left flex-column">
                    <div class="table-list-details table-list-details-with-icons <?php if ($show_project_description): ?>flex-row-space-between<?php else: ?>flex-row-space-around<?php endif ?>">
                        <?php if ($show_project_description): ?>
                            <div class="flex-column">
                                <h2><?= t('Summary') ?></h2>
                                <div class="project-metrics">
                                    <p><?= $project['is_active'] ? t('This project is open') : t('This project is closed') ?></p>

                                    <?php if ($project['owner_id'] > 0): ?>
                                        <div><?= t('Project owner: ') . $this->avatar->small($project['owner_id'], $project['owner_username'], $project['owner_name'], $project['owner_email'], $project['owner_path'], 'avatar-inline') . $this->text->e($project['owner_username']); ?></div>
                                    <?php endif ?>

                                    <?php if ($project['is_public']): ?>
                                        <p><?= $this->url->icon('share-alt', t('Public link'), 'BoardViewController', 'readonly', array('token' => $project['token']), false, '', '', true) ?></p>
                                        <p><?= $this->url->icon('rss-square', t('RSS feed'), 'FeedController', 'project', array('token' => $project['token']), false, '', '', true) ?></p>
                                        <p><?= $this->url->icon('calendar', t('iCal feed'), 'ICalendarController', 'project', array('token' => $project['token'])) ?></p>
                                    <?php else: ?>
                                        <p><?= t('Public access disabled') ?></p>
                                    <?php endif ?>

                                    <?php if ($project['last_modified']): ?>
                                        <p><?= t('Modified:') . ' ' . $this->dt->datetime($project['last_modified']) ?></p>
                                    <?php endif ?>

                                    <?php if ($project['start_date']): ?>
                                        <p><?= t('Start date: ') . ' ' . $this->dt->date($project['start_date']) ?></p>
                                    <?php endif ?>

                                    <?php if ($project['end_date']): ?>
                                        <p><?= t('End date: ') . ' ' . $this->dt->date($project['end_date']) ?></p>
                                    <?php endif ?>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if ($show_graphs): ?>
                            <?= $this->render('status:dashboard/userDistributionGraph', array(
                                'project_id' => $project['id'],
                                'metrics' => $userDistributionGraphs[$project['id']]
                            )) ?>

                            <?= $this->render('status:dashboard/taskDistributionGraph', array(
                                'project_id' => $project['id'],
                                'metrics' => $taskDistributionGraphs[$project['id']]
                            )) ?>
                        <?php endif ?>
                    </div>
                    <div class="table-border-left flex-row">
                        <div class="flex-column">
                            <h2><?= t('Project status') ?></h2>
                            <?php if ($this->user->hasProjectAccess('RecoveryPlanDetailController', 'deactivate', $project['id'])): ?>
                                <?php if ($recoveryPlans[$project['id']]): ?>
                                    <p><?= t('Last recovery plan') . ': ' . $this->url->link($this->dt->datetime($recoveryPlans[$project['id']]['date']), 'RecoveryPlanDetailController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'], 'recovery_plan_id' => $recoveryPlans[$project['id']]['id'])) ?></p>
                                    <?php if ($recoveryPlans[$project['id']]['date'] < $inActiveRecoveryPlans[$project['id']]['date']): ?>
                                        <p><?= t('Latest but inactive recovery plan') . ': ' . $this->url->link($this->dt->datetime($inActiveRecoveryPlans[$project['id']]['date']), 'RecoveryPlanDetailController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'], 'recovery_plan_id' => $inActiveRecoveryPlans[$project['id']]['id'])) ?></p>
                                    <?php endif ?>
                                <?php else: ?>
                                    <?php if ($inActiveRecoveryPlans[$project['id']]['date']): ?>
                                        <p><?= t('Latest but inactive recovery plan') . ': ' . $this->url->link($this->dt->datetime($inActiveRecoveryPlans[$project['id']]['date']), 'RecoveryPlanDetailController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'], 'recovery_plan_id' => $inActiveRecoveryPlans[$project['id']]['id'])) ?></p>
                                    <?php endif ?>
                                    <p><?= t('No recovery plan yet') . ': ' . $this->modal->medium('plus', t('New recovery plan'), 'RecoveryPlanCreateController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'])) ?></p>
                                <?php endif ?>


                                <div class="flex-row">
                                    <label for="quick"><?= t('Quick review') . ': ' ?></label>&nbsp
                                    <input type="checkbox" name="quick" id="quick" class="jtoggler"
                                           data-project-id="<?= $project['id'] ?>"
                                           data-current-value="<?= $project['project_status'] ?>" data-jtmulti-state>
                                </div>
                            <?php else: ?>
                                <?php if ($recoveryPlans[$project['id']]): ?>
                                    <span><?= t('Last recovery plan') . ': ' . $this->url->link($this->dt->date($recoveryPlans[$project['id']]['date']), 'RecoveryPlanDetailController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'], 'recovery_plan_id' => $recoveryPlans[$project['id']]['id'])) ?></span>
                                <?php endif ?>

                                <div class="flex-row">
                                    <label for="quick"><?= t('Quick review') . ': ' ?></label>&nbsp
                                    <input type="checkbox" name="quick" id="quick" class="jtoggler" disabled
                                           data-project-id="<?= $project['id'] ?>"
                                           data-current-value="<?= $project['project_status'] ?>" data-jtmulti-state>
                                </div>
                            <?php endif ?>
                        </div>
                        <?php if ($tasks[$project['id']]): ?>
                            <div class="table-border-left flex-row-space-around status-task-holder" style="flex: 1">
                                <?php foreach ($tasks[$project['id']] as $task): ?>
                                    <?= $this->render('status:dashboard/task', array(
                                        'project' => $project,
                                        'task' => $task,
                                    )) ?>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </div>

                    <?php if ($project['start_date'] && $project['end_date']): ?>
                        <progress
                                class="progress <?= $this->text->e('progress') . $project['id'] . ' ' . $this->text->e('color') . $project['project_status'] ?>"
                                value="<?= (time() - strtotime($project['start_date'])); ?>"
                                max="<?= t(strtotime($project['end_date']) - strtotime($project['start_date'])); ?>"
                        ></progress>
                    <?php endif ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <?= $paginator ?>
<?php endif ?>
