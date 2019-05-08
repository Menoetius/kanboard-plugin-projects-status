<section id="main">
    <div class="page-header">
        <h2><?= $title ?></h2>
    </div>
    <form id="review-creation-form" method="post"
          action="<?= $this->url->href('RecoveryPlanCreateController', 'save', array('plugin' => 'Status', 'project_id' => $project['id'])) ?>"
          autocomplete="off">
        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('id', $values) ?>

        <div class="flex-row-space-around">
            <div class="flex-column">
                <?= $this->form->label(t('Accomplished'), 'accomplished') ?>
                <?= $this->form->textEditor('accomplished', $values, $errors, array('tabindex' => 4)) ?>
            </div>
            <div class="flex-column">
                <?= $this->form->label(t('Plan'), 'plan') ?>
                <?= $this->form->textEditor('plan', $values, $errors, array('tabindex' => 4)) ?>
            </div>
        </div>

        <?php if(!isset($values['id'])): ?>
            <?= $this->form->checkbox('is_active', t('Show as actual recovery plan'), 1, isset($values['is_active']) && $values['is_active'] == 1) ?>
        <?php endif ?>

        <?= $this->modal->submitButtons() ?>
    </form>
</section>
