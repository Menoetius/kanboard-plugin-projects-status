<?php

namespace Kanboard\Plugin\Status\Model;

use Kanboard\Core\Base;
use Kanboard\Model\TaskModel;
use Kanboard\Model\UserModel;

/**
 * Issue model
 *
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 28.1.19
 */
class IssueModel extends Base
{

    /**
     * SQL table name for issue
     *
     * @var string
     */
    const TABLE = 'issue';

    /**
     * Checks if issue exists
     *
     * @param $issue_id
     * @return int
     */
    public function exists($issue_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $issue_id)->exists();
    }

    /**
     * Returns all issues of recovery plan
     *
     * @param $recovery_plan_id
     * @return array
     */
    public function getAllIssuesByRecoveryPlanId($recovery_plan_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.project_id',
                self::TABLE.'.recovery_plan_id',
                self::TABLE.'.task_id',
                self::TABLE.'.user_assignee',
                self::TABLE.'.user_issued',
                self::TABLE.'.name',
                self::TABLE.'.description',
                self::TABLE.'.due_date',
                self::TABLE.'.date_creation',
                self::TABLE.'.status',
                self::TABLE.'.completed',
                self::TABLE.'.position',
                self::TABLE.'.priority',
                TaskModel::TABLE.'.title',
                'assignee.username AS aUsername',
                'assignee.name AS aName',
                'assignee.email AS aEmail',
                'assignee.avatar_path AS aPath',
                'issued.username AS iUsername',
                'issued.name AS iName',
                'issued.email AS iEmail',
                'issued.avatar_path AS iPath'
            )
            ->join(TaskModel::TABLE, 'id', 'task_id', self::TABLE)
            ->left(UserModel::TABLE, 'assignee', 'id', self::TABLE, 'user_assignee')
            ->left(UserModel::TABLE, 'issued', 'id', self::TABLE, 'user_issued')
            ->eq(self::TABLE.'.recovery_plan_id', $recovery_plan_id)
            ->eq(self::TABLE.'.deleted', 0)
            ->asc(self::TABLE.'.position')
            ->findAll();
    }

    /**
     * Returns issue by its id
     *
     * @param $issue_id
     * @return array|null
     */
    public function getIssueById($issue_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.project_id',
                self::TABLE.'.recovery_plan_id',
                self::TABLE.'.task_id',
                self::TABLE.'.user_assignee',
                self::TABLE.'.user_issued',
                self::TABLE.'.name',
                self::TABLE.'.description',
                self::TABLE.'.due_date',
                self::TABLE.'.date_creation',
                self::TABLE.'.status',
                self::TABLE.'.completed',
                self::TABLE.'.position',
                self::TABLE.'.priority'
            )
            ->eq(self::TABLE.'.id', $issue_id)
            ->asc(self::TABLE.'.position')
            ->findOne();
    }

    /**
     * Saves issue to database
     *
     * @param array $values
     * @return bool|int
     */
    public function createIssue(array $values)
    {
        $this->db->startTransaction();
        $id = $values['id'];

        $this->helper->model->convertNullFields($values, array('user_assignee', 'task_id'));

        if ($this->exists($id)) {
            $this->prepare($values);

            if (!$this->db->table(self::TABLE)->eq('id', $id)->update($values)) {
                $this->db->cancelTransaction();
                return false;
            }

            $issue_id = $id;
        } else {
            if (!$this->db->table(self::TABLE)->save($values)) {
                $this->db->cancelTransaction();
                return false;
            }

            $issue_id = $this->db->getLastId();
        }
        


        $this->db->closeTransaction();

        return (int) $issue_id;
    }


    /**
     * Removes issue
     *
     * @access public
     * @param  integer $issue_id
     * @return bool
     */
    public function remove($issue_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $issue_id)->save(array('deleted' => 1));
    }


    /**
     * Changes issue position on recovery plan detail
     *
     * @access public
     * @param  integer  $recovery_plan_id
     * @param  integer  $issue_id
     * @param  integer  $position
     * @return boolean
     */
    public function changePosition($recovery_plan_id, $issue_id, $position)
    {
        if ($position < 1 || $position > $this->db->table(self::TABLE)->eq('recovery_plan_id', $recovery_plan_id)->count()) {
            return false;
        }

        $issue_ids = $this->db->table(self::TABLE)->eq('recovery_plan_id', $recovery_plan_id)->neq('id', $issue_id)->asc('position')->findAllByColumn('id');
        $offset = 1;
        $results = array();

        foreach ($issue_ids as $current_issue_id) {
            if ($offset == $position) {
                $offset++;
            }

            $results[] = $this->db->table(self::TABLE)->eq('id', $current_issue_id)->update(array('position' => $offset));
            $offset++;
        }

        $results[] = $this->db->table(self::TABLE)->eq('id', $issue_id)->update(array('position' => $position));

        return !in_array(false, $results, true);
    }

    /**
     * Prepares data before issue modification
     *
     * @access protected
     * @param  array  $values
     */
    protected function prepare(array &$values)
    {
        $values = $this->dateParser->convert($values, array('due_date'), true);
        $values = $this->dateParser->convert($values, array('date_creation'), true);

        $this->helper->model->removeFields($values, array('id'));
        $this->helper->model->resetFields($values, array('due_date', 'date_creation'));
        $this->helper->model->convertIntegerFields($values, array('priority', 'user_assignee', 'task_id'));
    }

    /**
     * Updates issue status
     *
     * @param $issue_id
     * @param $status
     * @return bool
     */
    public function changeStatus($issue_id, $status)
    {
        if ($status < 0) {
            return false;
        }

        $result = $this->db->table(self::TABLE)->eq('id', $issue_id)->update(array('status' => $status));

        return !in_array(false, $result, true);
    }
}
