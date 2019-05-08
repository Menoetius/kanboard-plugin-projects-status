<?php if (empty($metrics)): ?>
    <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
    <div class="columns_chart" id="columns_chart<?= $project_id ?>" data-chartid="<?= $project_id ?>" data-params='<?= json_encode($metrics, JSON_HEX_APOS) ?>'></div>
<?php endif ?>