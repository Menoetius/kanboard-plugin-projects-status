<div class="margin-bottom">
    <form method="get" action="<?= $this->url->dir() ?>" class="search">
        <?= $this->form->hidden('controller', array('controller' => 'StatusController')) ?>
        <?= $this->form->hidden('action', array('action' => 'index')) ?>
        <?= $this->form->hidden('plugin', array('plugin' => 'Status')) ?>

        <div class="input-addon">
            <?= $this->form->text('search', $values, array(), array('placeholder="'.t('Search').'"'), 'input-addon-field') ?>
            <div class="input-addon-item">
                <?= $this->render('status:dashboard/search_filter') ?>
            </div>
        </div>
    </form>
</div>