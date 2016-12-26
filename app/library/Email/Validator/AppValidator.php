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

use Phosphorum\Email\Warning\AppPart;
use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Warning\Warning;
use Egulias\EmailValidator\Exception\InvalidEmail;
use Egulias\EmailValidator\Validation\EmailValidation;

/**
 * Phosphorum\Email\Validator\AppValidator
 *
 * @package Phosphorum\Email\Validator
 */
class AppValidator implements EmailValidation
{
    /**
     * @var array
     */
    private $appParts = [];

    /**
     * @var array
     */
    private $warnings = [];

    /**
     * @var InvalidEmail
     */
    private $error;

    /**
     * AppValidator constructor.
     *
     * @param array $appParts
     */
    public function __construct(array $appParts)
    {
        $this->appParts = $appParts;
    }

    /**
     * Check if email length is not app.
     *
     * @param  string     $email      The email you want to validate
     * @param  EmailLexer $emailLexer The email lexer.
     * @return bool
     */
    public function isValid($email, EmailLexer $emailLexer)
    {
        return !$this->isApp($email);
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
    protected function isApp($email)
    {
        if (empty($this->appParts)) {
            return false;
        }

        $isApp = (bool) preg_match(sprintf('/^.*%s/iu', implode('|', $this->appParts)), $email);

        if ($isApp) {
            $this->warnings[AppPart::CODE] = new AppPart();
        }

        return $isApp;
    }
}
