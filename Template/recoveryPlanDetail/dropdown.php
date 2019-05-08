<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <li>
            <?= $this->modal->medium('edit', t('Edit'), 'IssueController', 'edit', array('plugin' => 'Status', 'issue_id' => $issue['id'], 'recovery_plan_id' => $recovery_plan_id, 'project_id' => $project_id)) ?>
        </li>
        <li>
            <?= $this->modal->confirm('trash-o', t('Remove'), 'IssueController', 'confirm', array('plugin' => 'Status', 'issue_id' => $issue['id'], 'recovery_plan_id' => $recovery_plan_id, 'project_id' => $project_id)) ?>
        </li>
    </ul>
</div>

