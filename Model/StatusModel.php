<?php

namespace Kanboard\Plugin\Status\Model;

use Kanboard\Core\Base;
use Kanboard\Model\TaskModel;
use Kanboard\Model\CommentModel;
use Kanboard\Model\TaskFileModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\TaskLinkModel;
use Kanboard\Model\TaskExternalLinkModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\ProjectModel;


/**
 * Model for finding tasks for project dashboard
 *
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 28.1.19
 */
class StatusModel extends Base
{


    /**
     * Returns up to 5 tasks of project that are after due_date or actual date plus estimated time is after due date
     *
     * @param $project_id
     * @return array
     */
    public function getHighlightedTasks($project_id)
    {
        return $this->db
            ->table(TaskModel::TABLE)
            ->columns(
                '(SELECT COUNT(*) FROM ' . CommentModel::TABLE . ' WHERE task_id=tasks.id) AS nb_comments',
                '(SELECT COUNT(*) FROM ' . TaskFileModel::TABLE . ' WHERE task_id=tasks.id) AS nb_files',
                '(SELECT COUNT(*) FROM ' . SubtaskModel::TABLE . ' WHERE ' . SubtaskModel::TABLE . '.task_id=tasks.id) AS nb_subtasks',
                '(SELECT COUNT(*) FROM ' . SubtaskModel::TABLE . ' WHERE ' . SubtaskModel::TABLE . '.task_id=tasks.id AND status=2) AS nb_completed_subtasks',
                '(SELECT COUNT(*) FROM ' . TaskLinkModel::TABLE . ' WHERE ' . TaskLinkModel::TABLE . '.task_id = tasks.id) AS nb_links',
                '(SELECT COUNT(*) FROM ' . TaskExternalLinkModel::TABLE . ' WHERE ' . TaskExternalLinkModel::TABLE . '.task_id = tasks.id) AS nb_external_links',
                '(SELECT DISTINCT 1 FROM ' . TaskLinkModel::TABLE . ' WHERE ' . TaskLinkModel::TABLE . '.task_id = tasks.id AND ' . TaskLinkModel::TABLE . '.link_id = 9) AS is_milestone',
                TaskModel::TABLE . '.id',
                TaskModel::TABLE . '.reference',
                TaskModel::TABLE . '.title',
                TaskModel::TABLE . '.description',
                TaskModel::TABLE . '.date_creation',
                TaskModel::TABLE . '.date_modification',
                TaskModel::TABLE . '.date_completed',
                TaskModel::TABLE . '.date_started',
                TaskModel::TABLE . '.date_due',
                TaskModel::TABLE . '.color_id',
                TaskModel::TABLE . '.project_id',
                TaskModel::TABLE . '.column_id',
                TaskModel::TABLE . '.swimlane_id',
                TaskModel::TABLE . '.owner_id',
                TaskModel::TABLE . '.creator_id',
                TaskModel::TABLE . '.position',
                TaskModel::TABLE . '.is_active',
                TaskModel::TABLE . '.score',
                TaskModel::TABLE . '.category_id',
                TaskModel::TABLE . '.priority',
                TaskModel::TABLE . '.date_moved',
                TaskModel::TABLE . '.recurrence_status',
                TaskModel::TABLE . '.recurrence_trigger',
                TaskModel::TABLE . '.recurrence_factor',
                TaskModel::TABLE . '.recurrence_timeframe',
                TaskModel::TABLE . '.recurrence_basedate',
                TaskModel::TABLE . '.recurrence_parent',
                TaskModel::TABLE . '.recurrence_child',
                TaskModel::TABLE . '.time_estimated',
                TaskModel::TABLE . '.time_spent',
                UserModel::TABLE . '.username AS assignee_username',
                UserModel::TABLE . '.name AS assignee_name',
                UserModel::TABLE . '.email AS assignee_email',
                UserModel::TABLE . '.avatar_path AS assignee_avatar_path',
                CategoryModel::TABLE . '.name AS category_name',
                CategoryModel::TABLE . '.description AS category_description',
                CategoryModel::TABLE . '.color_id AS category_color_id',
                ColumnModel::TABLE . '.title AS column_name',
                ColumnModel::TABLE . '.position AS column_position',
                SwimlaneModel::TABLE . '.name AS swimlane_name',
                ProjectModel::TABLE . '.name AS project_name'
            )
            ->eq(TaskModel::TABLE . '.project_id', $project_id)
            ->eq(TaskModel::TABLE . '.is_active', 1)
            ->neq(TaskModel::TABLE . '.date_due', 0)
            ->beginOr()
            ->lte(TaskModel::TABLE . '.date_due', time() . '+(' . TaskModel::TABLE . '.time_estimated-' . TaskModel::TABLE . '.time_spent)*1000')
            ->lte(TaskModel::TABLE . '.date_due', time())
            ->closeOr()
            ->desc(TaskModel::TABLE . '.priority')
            ->desc(TaskModel::TABLE . '.score')
            ->join(UserModel::TABLE, 'id', 'owner_id', TaskModel::TABLE)
            ->left(UserModel::TABLE, 'uc', 'id', TaskModel::TABLE, 'creator_id')
            ->join(CategoryModel::TABLE, 'id', 'category_id', TaskModel::TABLE)
            ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
            ->join(SwimlaneModel::TABLE, 'id', 'swimlane_id', TaskModel::TABLE)
            ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)
            ->limit(5)
            ->findAll();
    }


    /**
     * Returns last or active recovery plan of given project
     *
     * @param $project_id
     * @param $active
     * @return array|null
     */
    public function getLastRecoveryPlanOfProject($project_id, $active = 1)
    {
        return $this->db
            ->table(RecoveryPlanModel::TABLE)
            ->columns(
                RecoveryPlanModel::TABLE . '.id',
                RecoveryPlanModel::TABLE . '.date'
            )
            ->eq(RecoveryPlanModel::TABLE . '.project_id', $project_id)
            ->eq(RecoveryPlanModel::TABLE . '.deleted', 0)
            ->eq(RecoveryPlanModel::TABLE . '.is_active', $active)
            ->desc(RecoveryPlanModel::TABLE . '.date')
            ->findOne();
    }

    /**
     * Updates project status
     *
     * @param $project_id
     * @param $status_value
     * @return bool
     */
    public function changeProjectStatus($project_id, $status_value)
    {
        if ($status_value < 0 || $status_value > 2) {
            return false;
        }

        $result = $this->db->table(ProjectModel::TABLE)->eq('id', $project_id)->update(array('project_status' => $status_value));

        return $result;
    }

    /**
     * Gets all tasks for a given project
     *
     * @access public
     * @param  integer   $project_id      Project id
     * @return array
     */
    public function getAllTasksBy($project_id)
    {
        return $this->db
            ->table(TaskModel::TABLE)
            ->eq(TaskModel::TABLE.'.project_id', $project_id)
            ->asc(TaskModel::TABLE.'.id')
            ->findAll();
    }


    /**
     * Gets query for list of project with owner avatar
     *
     * @access public
     * @param  array $projectIds
     * @return \PicoDb\Table
     */
    public function getQueryByProjectIdsWithOwnerAvatar(array $projectIds)
    {
        if (empty($projectIds)) {
            return $this->db->table(ProjectModel::TABLE)->eq(ProjectModel::TABLE.'.id', 0);
        }

        return $this->db
            ->table(ProjectModel::TABLE)
            ->columns(ProjectModel::TABLE.'.*', UserModel::TABLE.'.username AS owner_username', UserModel::TABLE.'.name AS owner_name', UserModel::TABLE.'.email AS owner_email', UserModel::TABLE.'.avatar_path AS owner_path')
            ->join(UserModel::TABLE, 'id', 'owner_id')
            ->in(ProjectModel::TABLE.'.id', $projectIds);
    }
}
