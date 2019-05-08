<?php

namespace Kanboard\Plugin\Status\Model;

use Kanboard\Core\Base;
use Kanboard\Model\UserModel;

/**
 * Recovery plan model
 *
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 28.1.19
 */
class RecoveryPlanModel extends Base
{

    /**
     * SQL table name for recovery plan
     *
     * @var string
     */
    const TABLE = 'recovery_plan';

    /**
     * Checks if recovery plan exists
     *
     * @param $issue_id
     * @return int
     */
    public function exists($issue_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $issue_id)->exists();
    }

    /**
     * Saves new recovery plan to database
     *
     * @param array $values
     * @return bool|int
     */
    public function create(array $values)
    {
        $this->db->startTransaction();

        if ($values['is_active']){
            $this->deactivateOthers($values['project_id']);
        }

        if (!$this->db->table(self::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $recovery_plan_id = $this->db->getLastId();

        $this->db->closeTransaction();

        return (int)$recovery_plan_id;
    }

    /**
     * Updates existing recovery plan
     *
     * @param array $values
     * @return bool|int
     */
    public function update(array $values)
    {
        $this->db->startTransaction();

        if ($this->exists($values['id']) && $this->db->table(self::TABLE)->eq('id', $values['id'])->save($values)) {
            $this->db->closeTransaction();
            return $values['id'];
        }
        $this->db->cancelTransaction();
        return false;
    }

    /**
     * Returns recovery plan by its id
     *
     * @param $recovery_plan_id
     * @return array|null
     */
    public function getRecoveryPlanById($recovery_plan_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE . '.id',
                self::TABLE . '.project_id',
                self::TABLE . '.owner_id',
                self::TABLE . '.date',
                self::TABLE . '.accomplished',
                self::TABLE . '.plan',
                self::TABLE . '.is_active'
            )
            ->eq(self::TABLE . '.id', $recovery_plan_id)
            ->findOne();
    }

    /**
     * Returns recovery plan of project
     *
     * @param $project_id
     * @return array|null
     */
    public function getRecoveryPlansByProjectId($project_id)
    {
        $callback = function ($value) {
            return $value['id'];
        };

        return array_map($callback, $this->db
            ->table(self::TABLE)
            ->columns(self::TABLE . '.id')
            ->eq(self::TABLE . '.project_id', $project_id)
            ->eq(self::TABLE . '.deleted', 0)
            ->findAll());
    }

    /**
     * Removes recovery plan
     *
     * @access public
     * @param integer $recovery_plan_id
     * @return bool
     */
    public function remove($recovery_plan_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $recovery_plan_id)->save(array('deleted' => 1));
    }



    /**
     * Apply issue count to a collection of recoveryPlans (filter callback)
     *
     * @access public
     * @param  array    $recoveryPlans
     * @return array
     */
    public function applyIssueCount(array $recoveryPlans)
    {
        foreach ($recoveryPlans as &$recoveryPlan) {
            $recoveryPlan['issues'] = count($this->issueModel->getAllIssuesByRecoveryPlanId($recoveryPlan['id']));
        }

        return $recoveryPlans;
    }

    /**
     * Returns query for list of recovery plans without column statistics
     *
     * @access public
     * @param array $recoveryPlanIds
     * @return \PicoDb\Table
     */
    public function getQueryByRecoveryPlanIds(array $recoveryPlanIds)
    {
        if (empty($recoveryPlanIds)) {
            return $this->db->table(self::TABLE)->eq(self::TABLE . '.id', 0);
        }

        return $this->db
            ->table(self::TABLE)
            ->columns(self::TABLE . '.*', 'owner.username AS oUsername', 'owner.name AS oName', 'owner.email AS oEmail', 'owner.avatar_path AS oPath', 'modifier.username AS mUsername', 'modifier.name AS mName', 'modifier.email AS mEmail', 'modifier.avatar_path AS mPath')
            ->left(UserModel::TABLE, 'owner', 'id', self::TABLE, 'owner_id')
            ->left(UserModel::TABLE, 'modifier', 'id', self::TABLE, 'user_modified')
            ->eq(self::TABLE . '.deleted', 0)
            ->in(self::TABLE . '.id', $recoveryPlanIds)
            ->callback(array($this, 'applyIssueCount'));
    }

    /**
     * Makes given recovery plan active and other recovery plans of project inactive
     *
     * @access public
     * @param integer $recovery_plan_id
     * @param integer $project_id
     * @return bool
     */
    public function reactivate($recovery_plan_id, $project_id)
    {
        return $this->deactivateOthers($project_id) && $this->db->table(self::TABLE)->eq('id', $recovery_plan_id)->save(array('is_active' => 1));
    }

    /**
     * Makes all recovery plans of project inactive
     *
     * @access public
     * @param integer $project_id
     * @return boolean
     */
    public function deactivateOthers($project_id)
    {
        $recovery_plan_ids = $this->getRecoveryPlansByProjectId($project_id);
        $result = true;
        foreach ($recovery_plan_ids as $plan_id) {
            $result = $result && $this->deactivate($plan_id);
        }

        if (!$result) {
            $this->deactivateOthers($project_id);
        }

        return $result;
    }

    /**
     * Makes given recovery plan inactive
     *
     * @access public
     * @param integer $recovery_plan_id
     * @return bool
     */
    public function deactivate($recovery_plan_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $recovery_plan_id)->save(array('is_active' => 0));
    }
}
