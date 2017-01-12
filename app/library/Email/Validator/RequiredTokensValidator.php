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

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Warning\Warning;
use Phosphorum\Email\Exception\RequiredTokens;
use Egulias\EmailValidator\Exception\InvalidEmail;
use Egulias\EmailValidator\Validation\EmailValidation;

/**
 * Phosphorum\Email\Validator\RequiredTokensValidator
 *
 * @package Phosphorum\Email
 */
class RequiredTokensValidator implements EmailValidation
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
     * Check if  email has required tokens.
     *
     * Returns true if the given email is valid.
     *
     * @param  string     $email      The email you want to validate
     * @param  EmailLexer $emailLexer The email lexer.
     * @return bool
     */
    public function isValid($email, EmailLexer $emailLexer)
    {
        return $this->hasRequiredTokens($email);
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
    protected function hasRequiredTokens($email)
    {
        $hasRequiredTokens = strpos($email, '@') !== false && strpos($email, '.') !== false;

        if (!$hasRequiredTokens) {
            $this->error = new RequiredTokens();
        }

        return $hasRequiredTokens;
    }
}
