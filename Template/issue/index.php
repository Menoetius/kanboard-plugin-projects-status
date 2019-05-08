<section id="main">
    <div class="page-header">
        <h2><?= $title ?></h2>
    </div>
    <form id="issue-creation-form" method="post"
          action="<?= $this->url->href('IssueController', 'save', array('plugin' => 'Status', 'recovery_plan_id' => $recovery_plan['id'], 'project_id' => $project['id'])) ?>"
          autocomplete="off">
        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('id', $values) ?>

        <?= $this->form->label(t('Issue name'), 'name') ?>
        <?= $this->form->text('name', $values, $errors, array('autofocus', 'required')) ?>
        <?= $this->issue->renderUserField($users_list, $values, t('Issued by'), 'user_issued', $errors, array()) ?>

        <?= $this->issue->renderUserField($users_list, $values, t('Assignee'), 'user_assignee', $errors, array()) ?>
        <?= $this->issue->renderTaskField($tasks, $values, $errors) ?>
        <?= $this->issue->renderPriorityField($project, $values) ?>

        <?= $this->issue->renderDueDateField($values, $errors) ?>

        <?= $this->form->label(t('Description'), 'description') ?>
        <?= $this->form->textEditor('description', $values, $errors, array('tabindex' => 4)) ?>

        <?= $this->modal->submitButtons() ?>
    </form>
</section>
