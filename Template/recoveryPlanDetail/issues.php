<div class="recovery_plan_issues">
    <h3><?= t('Issues') . ':' ?></h3>
    <?php if (!empty($issues)): ?>
        <table
                class="issues-table table-striped table-scrolling"
                data-save-position-url="<?= $this->url->href('IssueController', 'movePosition', array('plugin' => 'Status', 'project_id' => $project['id'], 'recovery_plan_id' => $recovery_plan['id']))?>"
                data-save-status-url="<?= $this->url->href('RecoveryPlanDetailController', 'saveStatus', array('plugin' => 'Status', 'project_id' => $project['id']))?>"
        >
            <thead>
            <tr>
                <th class="column"><?= t('Title') ?></th>
                <th class="column"><?= t('Issued by') ?></th>
                <th class="column"><?= t('Status') ?></th>
                <th class="column"><?= t('Assignee') ?></th>
                <th class="column"><?= t('Due date') ?></th>
                <th class="column"><?= t('Priority') ?></th>
                <th class="column"><?= t('Referenced task') ?></th>
                <th class="column"><?= t('Description') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($issues as $issue): ?>
                <tr data-issue-id="<?= $issue['id'] ?>">
                    <td>
                        <?php if ($this->user->hasProjectAccess('IssueController', 'movePosition', $project['id'])): ?>
                        <i class="fa fa-arrows-alt draggable-row-handle" title="<?= t('Change issue position') ?>"></i>&nbsp
                        <?= $this->render('status:recoveryPlanDetail/dropdown', array(
                            'issue' => $issue,
                            'project_id' => $project['id'],
                            'recovery_plan_id' => $recovery_plan['id']
                        )); ?>
                        <?php endif ?>

                        <?= $this->url->link($this->text->e($issue['name']), 'IssueStepsController', 'index', array('plugin' => 'Status', 'issue_id' => $issue['id'], 'project_id' => $issue['project_id'])) ?>
                    </td>
                    <td>
                        <?php if (!empty($issue['user_issued'])): ?>
                            <?= $this->avatar->small($issue['user_issued'], $issue['iUsername'], $issue['iName'], $issue['iEmail'], $issue['iPath'], 'avatar-inline') . $this->text->e($issue['iUsername']); ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if ($issue['user_assignee'] == $this->user->getId() || $this->user->hasProjectAccess('IssueController', 'movePosition', $project['id'])): ?>
                            <?= $this->helper->form->select('status', $issue_status_options, array('status' =>  $issue['status']), array(), array('data-id='.$issue['id']), 'status' . $issue['id']) ?>
                        <?php else: ?>
                            <?= $issue_status_options[$issue['status']] ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if (!empty($issue['user_assignee'])): ?>
                            <?= $this->avatar->small($issue['user_assignee'], $issue['aUsername'], $issue['aName'], $issue['aEmail'], $issue['aPath'], 'avatar-inline') . $this->text->e($issue['aUsername']); ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if (!empty($issue['due_date'])): ?>
                            <?= $this->dt->date($issue['due_date']) ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if (!empty($issue['priority'])): ?>
                            <?= $this->text->e($issue['priority']) ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if (!empty($issue['task_id'])): ?>
                            <?= $this->url->link($this->text->e($issue['title']), 'TaskViewController', 'show', array('task_id' => $issue['task_id'], 'project_id' => $project['id'])) ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if (!empty($issue['description'])): ?>
                        <article class="markdown">
                            <?= $this->text->markdown(strlen($issue['description']) > 50 ? substr($this->text->e($issue['description']), 0, 50) . '...' : $this->text->e($issue['description'])) ?>
                        </article>

                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><?= t('No issues.') ?></p>
    <?php endif ?>
</div>
