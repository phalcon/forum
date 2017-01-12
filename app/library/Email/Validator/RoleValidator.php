<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Email\Validator;

use Phosphorum\Email\Warning\RolePart;
use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Warning\Warning;
use Egulias\EmailValidator\Exception\InvalidEmail;
use Egulias\EmailValidator\Validation\EmailValidation;

/**
 * Phosphorum\Email\Validator\RoleValidator
 *
 * @package Phosphorum\Email\Validator
 */
class RoleValidator implements EmailValidation
{
    /**
     * @var array
     */
    private $roleParts = [];

    /**
     * @var array
     */
    private $warnings = [];

    /**
     * @var InvalidEmail
     */
    private $error;

    /**
     * RoleValidator constructor.
     *
     * @param array $roleParts
     */
    public function __construct(array $roleParts)
    {
        $this->roleParts = $roleParts;
    }

    /**
     * Check if email length is not role-based.
     *
     * @param  string     $email      The email you want to validate
     * @param  EmailLexer $emailLexer The email lexer.
     * @return bool
     */
    public function isValid($email, EmailLexer $emailLexer)
    {
        return !$this->isRole($email);
    }

    /**
     * Returns the validation error.
     *
     * @return InvalidEmail|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Returns the validation warnings.
     *
     * @return Warning[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @param  string $email
     * @return bool
     */
    protected function isRole($email)
    {
        if (empty($this->roleParts)) {
            return false;
        }

        $isRole = (bool) preg_match(sprintf('/^(?:%s).*/iu', implode('|', $this->roleParts)), $email);

        if ($isRole) {
            $this->warnings[RolePart::CODE] = new RolePart();
        }

        return $isRole;
    }
}
