<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is no recovery plan.') ?></p>
<?php else: ?>
    <div class="table-list switch-wrapper">
        <div class="table-list-header flex-row-space-between">
            <div class="table-list-header-count">
                <?php if (count($recovery_plans) > 1): ?>
                    <?= t('%d reviews', count($recovery_plans)) ?>
                <?php else: ?>
                    <?= t('%d review', count($recovery_plans)) ?>
                <?php endif ?>
            </div>
            <div class="table-list-header-menu">
                <?= $this->render('status:recoveryPlanList/sort', array('paginator' => $paginator)) ?>
            </div>

        </div>
        <?php foreach ($paginator->getCollection() as $recoveryPlan): ?>
            <div class="table-list-row table-border-left flex-row-space-between <?= !$recoveryPlan['is_active'] ? $this->text->e('deleted') : '' ?> ">
                <?php if ($this->user->hasProjectAccess('RecoveryPlanListController', 'reactivate', $project['id'])): ?>
                    <div class="list-small">
                        <?= $this->render('status:recoveryPlanList/dropdown', array('recovery_plan_id' => $recoveryPlan['id'], 'project_id' => $recoveryPlan['project_id'], 'deleted' => !$recoveryPlan['is_active'])) ?>
                    </div>
                <?php elseif($recoveryPlan['is_active']): ?>
                    <?= $this->url->link('#'.$recoveryPlan['id'], 'RecoveryPlanDetailController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'], 'recovery_plan_id' => $recoveryPlan['id'])) ?>
                <?php else: ?>
                    <?= '#'.$recoveryPlan['id'] ?>
                <?php endif ?>
                <div class="list-width">
                    <?= t('Created by: ') . $this->avatar->small($recoveryPlan['owner_id'], $recoveryPlan['oUsername'], $recoveryPlan['oName'], $recoveryPlan['oEmail'], $recoveryPlan['oPath'], 'avatar-inline') . $this->text->e($recoveryPlan['oUsername']); ?>
                </div>
                <div class="list-width">
                    <?= t('Created: ') . $this->dt->datetime($recoveryPlan['date']) ?>
                </div>
                <div class="list-width">
                    <?= t('Issues: ') . $recoveryPlan['issues'] ?>
                </div>
                <div class="list-width">
                    <?php if ($recoveryPlan['last_modified']): ?>
                        <?= t('Last modified: ') . $this->dt->datetime($recoveryPlan['last_modified']) ?>
                    <?php else: ?>
                        <?= t('Last modified: ') . t('Original') ?>
                    <?php endif ?>
                </div>
                <div class="list-width">
                    <?php if ($recoveryPlan['last_modified']): ?>
                        <?= t('Modified by: ') . $this->avatar->small($recoveryPlan['user_modified'], $recoveryPlan['mUsername'], $recoveryPlan['mName'], $recoveryPlan['mEmail'], $recoveryPlan['mPath'], 'avatar-inline') . $this->text->e($recoveryPlan['mUsername']) ?>
                    <?php else: ?>
                        <?= t('Modified by: ') . t('---') ?>
                    <?php endif ?>
                </div>
                <div class="list-small">
                    <?= !$recoveryPlan['is_active'] ? t('Deactivated') : '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp' ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <?= $paginator ?>
<?php endif ?>