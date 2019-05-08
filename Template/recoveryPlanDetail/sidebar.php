<div class="sidebar sidebar-icons">
    <div class="sidebar-title">
        <h2><?= t('Actions') ?></h2>
    </div>
    <ul>
        <?php if ($this->user->hasProjectAccess('RecoveryPlanCreateController', 'index', $project['id'])): ?>
            <li>
                <?= $this->modal->medium('plus', t('Make new recovery plan'), 'RecoveryPlanCreateController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>
        <li>
            <?= $this->url->icon('list', t('List of recovery plans'), 'RecoveryPlanListController', 'index', array('plugin' => 'Status', 'project_id' => $project['id'])) ?>
        </li>
        <?php if ($this->user->hasProjectAccess('RecoveryPlanCreateController', 'edit', $project['id'])): ?>
            <li>
                <?= $this->modal->medium('edit', t('Edit current recovery plan'), 'RecoveryPlanCreateController', 'edit', array('plugin' => 'Status', 'recovery_plan_id' => $recovery_plan['id'], 'project_id' => $project['id'])) ?>
            </li>
            <li>
                <?= $this->modal->medium('plus', t('Add an issue'), 'IssueController', 'index', array('plugin' => 'Status', 'recovery_plan_id' => $recovery_plan['id'], 'project_id' => $project['id'])) ?>
            </li>
            <?php if (!$recovery_plan['is_active']): ?>
                <li>
                    <?= $this->modal->confirm('refresh', t('Make active again'), 'RecoveryPlanDetailController', 'reactivate', array('plugin' => 'Status', 'recovery_plan_id' => $recovery_plan['id'], 'project_id' => $project['id'])) ?>
                </li>
            <?php else: ?>
                <li>
                    <?= $this->modal->confirm('times', t('Deactivate'), 'RecoveryPlanDetailController', 'deactivate', array('plugin' => 'Status', 'recovery_plan_id' => $recovery_plan['id'], 'project_id' => $project['id'])) ?>
                </li>
            <?php endif ?>
            <li>
                <?= $this->modal->confirm('trash', t('Remove'), 'RecoveryPlanDetailController', 'removeConfirm', array('plugin' => 'Status', 'recovery_plan_id' => $recovery_plan['id'], 'project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>
    </ul>
</div>