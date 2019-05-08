<section class="main">
    <?= $this->render('status:recoveryPlanList/menu', array()) ?>

    <?= $this->render('status:recoveryPlanList/list', array('paginator' => $paginator, 'project' => $project, 'recovery_plans' => $recovery_plans)) ?>
</section>
