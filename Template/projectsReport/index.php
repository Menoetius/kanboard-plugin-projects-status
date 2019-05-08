<section class="main">
    <?= $this->render('status:projectsReport/menu', array()) ?>

    <?= $this->render('status:projectsReport/table', array('data' => $data, 'issues' => $issues)) ?>
</section>
