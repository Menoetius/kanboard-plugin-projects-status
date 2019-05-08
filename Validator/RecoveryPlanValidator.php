<?php

namespace Kanboard\Plugin\Status\Validator;

use Kanboard\Validator\BaseValidator;
use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Recovery plan Validator
 *
 * @package  Kanboard\Plugin\Status\Validator
 */
class RecoveryPlanValidator extends BaseValidator
{

    /**
     * Common validation rules for recovery plan
     * @return array
     */
    public function commonRules()
    {
        return array(
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Integer('owner_id', t('This value must be an integer')),
            new Validators\Integer('user_modifier', t('This value must be an integer')),
            new Validators\Integer('is_active', t('This value must be an integer')),
        );
    }

    /**
     * Validate recovery plan creation
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
     * Validate recovery plan modification
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
