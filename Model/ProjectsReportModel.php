<?php

namespace Kanboard\Plugin\Status\Model;

use Kanboard\Core\Base;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\UserModel;

/**
 * Model for finding data for user projects report
 *
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 28.1.19
 */
class ProjectsReportModel extends Base
{
    /**
     *  Returns array of data for projects report table
     *
     * @param $projects_ids
     * @return array
     */
    public function getTableData($projects_ids)
    {

        return $this->db
            ->table(RecoveryPlanModel::TABLE)
            ->columns(
                RecoveryPlanModel::TABLE.'.id',
                RecoveryPlanModel::TABLE.'.date',
                RecoveryPlanModel::TABLE.'.accomplished',
                RecoveryPlanModel::TABLE.'.plan',
                UserModel::TABLE.'.username AS oUsername',
                UserModel::TABLE.'.name AS oName',
                UserModel::TABLE.'.email AS oEmail',
                UserModel::TABLE.'.avatar_path AS oPath',
                ProjectModel::TABLE.'.name AS project_name',
                ProjectModel::TABLE.'.project_status',
                ProjectModel::TABLE.'.is_active',
                ProjectModel::TABLE.'.end_date'
            )
            ->join(UserModel::TABLE, 'id', 'owner_id', RecoveryPlanModel::TABLE)
            ->join(ProjectModel::TABLE, 'id', 'project_id', RecoveryPlanModel::TABLE)
            ->eq(ProjectModel::TABLE.'.is_active', 1)
            ->eq(RecoveryPlanModel::TABLE.'.is_active', 1)
            ->eq(RecoveryPlanModel::TABLE . '.deleted', 0)
            ->in(RecoveryPlanModel::TABLE.'.project_id', $projects_ids)
            ->findAll();
    }
}
