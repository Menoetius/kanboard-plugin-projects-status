<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon" title="<?= t('Default filters') ?>"><i class="fa fa-filter fa-fw"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <li><a href="#" class="filter-helper filter-reset" data-filter="<?= isset($reset) ? $reset : '' ?>" title="<?= t('Keyboard shortcut: "%s"', 'r') ?>"><?= t('Reset filters') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:green"><?= t('Projects with good status') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:amber"><?= t('Projects with average status') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:red"><?= t('Projects with bad status') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="owner:me"><?= t('My projects') ?></a></li>
        <li>
            <?= $this->url->doc(t('View advanced search syntax'), 'search') ?>
        </li>
    </ul>
</div>