<section id="main">
    <div class="page-header">
        <h2><?= $title ?></h2>
    </div>
    <form id="issue-creation-form" method="post"
          action="<?= $this->url->href('IssueStepsController', 'save', array('plugin' => 'Status', 'issue_id' => $issue_id, 'recovery_plan_id' => $recovery_plan_id,  'project_id' => $project['id'])) ?>"
          autocomplete="off">
        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('id', $values) ?>

        <?= $this->form->textEditor('text', $values, $errors, array('tabindex' => 4)) ?>

        <?= $this->modal->submitButtons() ?>
    </form>
</section>
