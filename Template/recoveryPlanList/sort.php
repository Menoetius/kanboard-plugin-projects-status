<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong><?= t('Sort') ?> <i class="fa fa-caret-down"></i></strong></a>
    <ul>
        <li>
            <?= $paginator->order(t('Recovery plan ID'), 'recovery_plan'.'.id') ?>
        </li>
        <li>
            <?= $paginator->order(t('Last modified'), 'recovery_plan'.'.last_modified') ?>
        </li>
        <li>
            <?= $paginator->order(t('Owner ID'), 'recovery_plan'.'.owner_id') ?>
        </li>
        <li>
            <?= $paginator->order(t('Creation date'), 'recovery_plan'.'.date') ?>
        </li>
        <li>
            <?= $paginator->order(t('Modified by'), 'recovery_plan'.'.user_modified') ?>
        </li>
    </ul>
</div>
