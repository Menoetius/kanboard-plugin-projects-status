<?php

namespace Kanboard\Plugin\Status\Model;

use Kanboard\Core\Base;
use Kanboard\Model\UserModel;

/**
 * Issue steps model
 *
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 28.1.19
 */
class IssueStepsModel extends Base
{

    /**
     * SQL table name for issue steps
     *
     * @var string
     */
    const TABLE = 'issue_steps';

    /**
     * Checks if issue step exists
     *
     * @param $issue_steps_id
     * @return int
     */
    public function exists($issue_steps_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $issue_steps_id)->exists();
    }

    /**
     * Returns all issue steps of issue
     *
     * @param $issue_id
     * @return array
     */
    public function getAllStepsByIssueId($issue_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.issue_id',
                self::TABLE.'.recovery_plan_id',
                self::TABLE.'.owner_id',
                self::TABLE.'.date_creation',
                self::TABLE.'.text',
                self::TABLE.'.deleted',
                UserModel::TABLE.'.username AS username',
                UserModel::TABLE.'.name AS name',
                UserModel::TABLE.'.email AS email',
                UserModel::TABLE.'.avatar_path AS path'
            )
            ->join(UserModel::TABLE, 'id', 'owner_id', self::TABLE)
            ->eq(self::TABLE.'.issue_id', $issue_id)
            ->eq(self::TABLE.'.deleted', 0)
            ->asc(self::TABLE.'.date_creation')
            ->findAll();
    }

    /**
     * Returns issue step by its id
     *
     * @param $issue_steps_id
     * @return array|null
     */
    public function getIssueStepsById($issue_steps_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.issue_id',
                self::TABLE.'.recovery_plan_id',
                self::TABLE.'.owner_id',
                self::TABLE.'.date_creation',
                self::TABLE.'.text',
                self::TABLE.'.deleted'
            )
            ->eq(self::TABLE.'.id', $issue_steps_id)
            ->asc(self::TABLE.'.date_creation')
            ->findOne();
    }

    /**
     * Saves issue step to database
     *
     * @param array $values
     * @return bool|int
     */
    public function createIssueSteps(array $values)
    {
        $this->db->startTransaction();

        if ($this->exists($values['id'])) {
            if (!$this->db->table(self::TABLE)->eq('id', $values['id'])->update($values)) {
                $this->db->cancelTransaction();
                return false;
            }

            $issue_id = $values['id'];
        } else {
            if (!$this->db->table(self::TABLE)->save($values)) {
                $this->db->cancelTransaction();
                return false;
            }

            $issue_id = $this->db->getLastId();
        }

        $this->db->closeTransaction();

        return (int)$issue_id;
    }

    /**
     * Removes issue step
     *
     * @access public
     * @param  integer $issue_step_id
     * @return bool
     */
    public function remove($issue_step_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $issue_step_id)->save(array('deleted' => 1));
    }
}
