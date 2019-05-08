<div class="issue_steps_content">
    <div class="recovery_plan_info flex-row-space-around">
        <div class="flex-grow"> <?= t('Assigned to') . ': ' . $this->avatar->small($assignee['id'], $assignee['username'], $assignee['name'], $assignee['email'], $assignee['path'], 'avatar-inline') . $this->text->e($assignee['username']); ?></div>
        <p class="flex-grow"> <?= t('Due date') . ': ' . $this->dt->date($issue['due_date']) ?></p>
    </div>

    <p> <?= t('Description') . ': ' ?> </p>
    <article class="markdown textarea">
        <?= $this->text->markdown($issue['description']) ?>
    </article>

    <?php foreach ($steps as $comment): ?>
        <div class="issue-comment">
            <div class="issue-comment-header flex-row-space-between">
                <div><?= $this->avatar->small($comment['owner_id'], $comment['username'], $comment['name'], $comment['email'], $comment['path'], 'avatar-inline') . $this->text->e($comment['username']) . ' ' . t('commented on') . ' ' . $this->dt->date($comment['date_creation']); ?></div>
                <?php if ($comment['owner_id'] == $this->user->getId() || $this->userSession->isAdmin()): ?>
                    <?= $this->render('status:issueSteps/dropdown', array(
                        'issue_step_id' => $comment['id'],
                        'project_id' => $project_id,
                        'issue_id' => $issue['id'],
                        'recovery_plan_id' => $issue['recovery_plan_id']
                    )); ?>

                <?php endif ?>
            </div>
            <article class="markdown textarea">
                <?= $this->text->markdown($comment['text']) ?>
            </article>
        </div>
    <?php endforeach ?>

    <h3><?= t('Write new comment') . ':' ?></h3>
    <form method="post"
          action="<?= $this->url->href('IssueStepsController', 'save', array('plugin' => 'Status', 'issue_id' => $issue['id'], 'project_id' => $issue['project_id'], 'recovery_plan_id' => $issue['recovery_plan_id'])) ?>"
          autocomplete="off">
        <?= $this->form->csrf() ?>

        <?= $this->form->textEditor('text', array(), array(), array('required' => true)) ?>
        <?= $this->modal->submitButtons() ?>
    </form>
</div>