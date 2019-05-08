<?php

namespace Kanboard\Plugin\Status\Validator;

use Kanboard\Validator\BaseValidator;
use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Issue Validator
 *
 * @package  Kanboard\Plugin\Status\Validator
 */
class IssueValidator extends BaseValidator
{

    /**
     * Common validation rules for issue
     * @return array
     */
    public function commonRules()
    {
        return array(
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Integer('recovery_plan_id', t('This value must be an integer')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Integer('task_id', t('This value must be an integer')),
            new Validators\Integer('user_assignee', t('This value must be an integer')),
            new Validators\Integer('user_assignee', t('This value must be an integer')),
            new Validators\Integer('priority', t('This value must be an integer')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 255), 255),
            new Validators\Required('name', t('The issue name is required')),
        );
    }

    /**
     * Validate issue creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, $this->commonRules());

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate issue modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The id is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
