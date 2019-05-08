<div class="page-header">
    <h2><?= t('Project status settings') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('SettingsController', 'save', array('plugin' => 'Status')) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <fieldset>
        <legend><?= t('Project status main page') ?></legend>
        <?= $this->form->checkboxes('status_project_dashboard', array(
                'critical_tasks' => t('Show critical project tasks'),
                'projects_graphs' => t('Show analytics charts for projects'),
                'project_description' => t('Show detail information of projects')
            ),
            $values
        ) ?>
    </fieldset>

    <fieldset>
        <legend><?= t('How many projects should be on one page of dashboard') ?></legend>
        <?= $this->form->number('status_project_pagination', $values, array(), array('min="1"')) ?>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>
