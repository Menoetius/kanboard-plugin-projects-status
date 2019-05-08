<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong>#<?= $recovery_plan_id ?> <i
                    class="fa fa-caret-down"></i></strong></a>
    <ul>
        <li>
            <?= $this->url->icon('edit', t('Edit'), 'RecoveryPlanDetailController', 'index', array('plugin' => 'Status', 'recovery_plan_id' => $recovery_plan_id, 'project_id' => $project_id)) ?>
        </li>
        <?php if ($deleted): ?>
            <li>
                <?= $this->modal->confirm('refresh', t('Make active again'), 'RecoveryPlanListController', 'reactivate', array('plugin' => 'Status', 'recovery_plan_id' => $recovery_plan_id, 'project_id' => $project_id)) ?>
            </li>
        <?php else: ?>
            <li>
                <?= $this->modal->confirm('times', t('Deactivate'), 'RecoveryPlanListController', 'deactivate', array('plugin' => 'Status', 'recovery_plan_id' => $recovery_plan_id, 'project_id' => $project_id)) ?>
            </li>
        <?php endif ?>
    </ul>
</div>