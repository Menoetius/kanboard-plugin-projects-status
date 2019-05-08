<?php
/**
 * Created by PhpStorm.
 * User: xmasar12
 * Date: 28.2.19
 * Time: 18:52
 */


namespace Kanboard\Plugin\Status\Helper;

use Kanboard\Core\Base;

/**
 * Class IssueHelper
 * @package Kanboard\Plugin\Status\Helper
 */
class IssueHelper extends Base
{
    /**
     * @param array $users
     * @param array $values
     * @param array $errors
     * @param array $attributes
     * @param string $label
     * @param string $name
     * @return string
     */
    public function renderUserField(array $users, array $values, $label, $name, array $errors = array(), array $attributes = array())
    {

        $attributes = array_merge(array('tabindex="3"'), $attributes);

        $html = $this->helper->form->label($label, $name);
        $html .= $this->helper->form->select($name, $users, $values, $errors, $attributes);
        $html .= '&nbsp;';
        $html .= '<small>';
        $html .= '<a href="#" class="assign-me" data-target-id="form-' . $name . '" data-current-id="'.$this->userSession->getId().'" title="'.t('Assign to me').'">'.t('Me').'</a>';
        $html .= '</small>';

        return $html;
    }


    /**
     * @param array $project
     * @param array $values
     * @return string
     */
    public function renderPriorityField(array $project, array $values)
    {
        $range = range($project['priority_start'], $project['priority_end']);
        $options = array_combine($range, $range);
        $values += array('priority' => $project['priority_default']);

        $html = $this->helper->form->label(t('Priority'), 'priority');
        $html .= $this->helper->form->select('priority', $options, $values, array(), array('tabindex="7"'));

        return $html;
    }

    /**
     * @param array $values
     * @param array $errors
     * @param array $attributes
     * @return string
     */
    public function renderDueDateField(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="9"'), $attributes);
        return $this->helper->form->datetime(t('Due Date'), 'due_date', $values, $errors, $attributes);
    }

    /**
     * @param array $tasks
     * @param array $values
     * @param array $errors
     * @param array $attributes
     * @return string
     */
    public function renderTaskField(array $tasks, array $values, array $errors = array(), array $attributes = array())
    {

        $attributes = array_merge(array('tabindex="3"'), $attributes);

        $html = $this->helper->form->label(t('Referenced task'), 'task_id');
        $html .= $this->helper->form->select('task_id', $tasks, $values, $errors, $attributes);

        return $html;
    }

}