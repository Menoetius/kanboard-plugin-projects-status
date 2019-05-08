<div class="
        task-board
        <?= $task['is_active'] == 1 ? 'task-board-status-open ' . ($task['date_modification'] > (time() - $board_highlight_period) ? 'task-board-recent' : '') : 'task-board-status-closed' ?>
        color-<?= $task['color_id'] ?>"
     data-task-id="<?= $task['id'] ?>"
     data-owner-id="<?= $task['owner_id'] ?>"
     data-category-id="<?= $task['category_id'] ?>"
     data-due-date="<?= $task['date_due'] ?>"
     data-task-url="<?= $this->url->href('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"
    style="float: left"
>
    <div class="task-board-sort-handle" style="display: none;"><i class="fa fa-arrows-alt"></i></div>

    <div class="task-board-expanded">
        <div class="task-board-saving-icon" style="display: none;"><i class="fa fa-spinner fa-pulse fa-2x"></i></div>
        <div class="task-board-header">
            <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
                <?= $this->render('task/dropdown', array('task' => $task, 'redirect' => 'board')) ?>
                <?= $this->modal->large('edit', '', 'TaskModificationController', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            <?php else: ?>
                <strong><?= '#' . $task['id'] ?></strong>
            <?php endif ?>

            <?php if (!empty($task['owner_id'])): ?>
                <span class="task-board-assignee">
                        <?= $this->text->e($task['assignee_name'] ?: $task['assignee_username']) ?>
                    </span>
            <?php endif ?>

            <?= $this->render('board/task_avatar', array('task' => $task)) ?>
        </div>

        <?= $this->hook->render('template:board:private:task:before-title', array('task' => $task)) ?>
        <div class="task-board-title">
            <?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </div>
        <?= $this->hook->render('template:board:private:task:after-title', array('task' => $task)) ?>

        <?= $this->render('board/task_footer', array(
            'task' => $task,
            'not_editable' => false,
            'project' => $project,
        )) ?>
    </div>
</div>
