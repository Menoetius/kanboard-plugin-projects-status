<section class="main">
        <?= $this->render('status:recoveryPlanDetail/menu', array()) ?>
    <section class="sidebar-container">
        <?= $this->render('status:recoveryPlanDetail/sidebar', array('project' => $project, 'recovery_plan' => $recovery_plan)) ?>

        <div class="sidebar-content">
            <?= $this->render('status:recoveryPlanDetail/content', array('recovery_plan' => $recovery_plan, 'creator' => $creator, 'project' => $project)) ?>

            <?= $this->render('status:recoveryPlanDetail/issues', array('recovery_plan' => $recovery_plan, 'issues' => $issues, 'project' => $project, 'issue_status_options' => $issue_status_options)) ?>
        </div>
    </section>
</section>
