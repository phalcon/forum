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

use Phosphorum\Email\EmailComponent;
use Phosphorum\Email\Exception\Length;
use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Warning\Warning;
use Egulias\EmailValidator\Exception\InvalidEmail;
use Egulias\EmailValidator\Validation\EmailValidation;

/**
 * Phosphorum\Email\Validator\LengthValidator
 *
 * @package Phosphorum\Email
 */
class LengthValidator implements EmailValidation
{
    /**
     * @var array
     */
    private $warnings = [];

    /**
     * @var InvalidEmail
     */
    private $error;

    /**
     * Check if  email length is allowed.
     * Email containing greater than 75 and less than 5 chars always invalid. At least for ASCII.
     *
     * Returns true if the given email is valid.
     *
     * @param  string     $email      The email you want to validate
     * @param  EmailLexer $emailLexer The email lexer.
     * @return bool
     */
    public function isValid($email, EmailLexer $emailLexer)
    {
        return $this->checkLength($email);
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
    protected function checkLength($email)
    {
        if (function_exists('mb_strlen')) {
            $length = mb_strlen($email, 'utf-8');
        } else {
            $length = strlen($email);
        }

        $hasAllowedLen = EmailComponent::MAX_LEN >= $length && EmailComponent::MIN_LEN <= $length;

        if (!$hasAllowedLen) {
            $this->error = new Length();
        }

        return $hasAllowedLen;
    }
}
