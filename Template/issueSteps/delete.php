<div class="page-header">
    <h2><?= $title ?></h2>
</div>

<div class="confirm">
    <form id="review-creation-form" method="post"
          action="<?= $this->url->href('IssueStepsController', 'remove', array('plugin' => 'Status', 'issue_id' => $issue_id, 'project_id' => $project_id))?>"
          autocomplete="off">
        <div class="alert alert-info">
            <?= t('Do you really want to remove this commentary?') ?>
        </div>
        <?= $this->form->csrf(); ?>
        <?= $this->form->hidden('id', $values); ?>
        <?= $this->modal->submitButtons(); ?>
    </form>
</div>
