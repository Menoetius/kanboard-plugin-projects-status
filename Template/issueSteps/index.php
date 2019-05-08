<section class="main">
    <?= $this->render('status:issueSteps/menu', array()) ?>

    <?= $this->render('status:issueSteps/content', array('project_id' => $project['id'], 'issue' => $issue, 'steps' => $steps, 'assignee' => $assignee)) ?>
</section>
