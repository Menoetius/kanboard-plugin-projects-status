<section class="main">
    <?= $this->render('status:dashboard/menu', array()) ?>

    <?= $this->render('status:dashboard/search', array('values' => $values)) ?>

    <?= $this->render('status:dashboard/content', array(
        'paginator'   => $paginator,
        'tasks' => $tasks,
        'userDistributionGraphs' => $userDistributionGraphs,
        'taskDistributionGraphs' => $taskDistributionGraphs,
        'show_graphs' => $show_graphs,
        'show_project_description' => $show_project_description,
        'recoveryPlans' => $recoveryPlans,
        'inActiveRecoveryPlans' => $inActiveRecoveryPlans,
    )) ?>
</section>
