<div class="page-header">
    <h2><?= $title ?></h2>
</div>

<div class="confirm">
    <form id="review-creation-form" method="post"
          action="<?= $this->url->href('IssueController', 'remove', array('plugin' => 'Status', 'project_id' => $project_id, 'recovery_plan_id' => $recovery_plan_id)) ?>"
          autocomplete="off">
        <div class="alert alert-info">
            <?= t('Do you really want to remove this issue?') ?>
            <ul>
                <li>
                    <strong><?= $this->text->e($issue['name']) ?></strong>
                </li>
            </ul>
        </div>
        <?= $this->form->csrf(); ?>
        <?= $this->form->hidden('id', $values); ?>
        <?= $this->modal->submitButtons(); ?>
    </form>
</div>
