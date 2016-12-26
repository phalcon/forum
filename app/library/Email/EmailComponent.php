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

namespace Phosphorum\Email;

use Phalcon\Config;
use Phalcon\Di\Injectable;
use Egulias\EmailValidator\EmailValidator;
use Phosphorum\Email\Validator\AppValidator;
use Phosphorum\Email\Validator\RoleValidator;
use Phosphorum\Email\Validator\CorpValidator;
use Phosphorum\Email\Validator\LengthValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Phosphorum\Email\Validator\RequiredTokensValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;

/**
 * Phosphorum\Email\EmailComponent
 *
 * @package Phosphorum\Email
 */
class EmailComponent extends Injectable
{
    /**
     * Max email len
     * Following RFC 5321 section 4.5.3.1.1 local parts should not exceed 64 octets.
     * Also, domain parts can not exceed 254 octets (RFC 5321 section 4.5.3.1.2).
     * @type string
     */
    const MAX_LEN = 319;

    /**
     * Min email len
     * @type string
     */
    const MIN_LEN = 5;

    /**
     * Email Address
     * @var string
     */
    private $email;

    /**
     * Component Config
     * @var Config
     */
    private $config;

    /**
     * Email constructor.
     *
     * @param string $email    Email Address
     * @param bool   $sanitize Sanitize?
     */
    public function __construct($email, $sanitize = true)
    {
        $this->email  = mb_strtolower(rawurldecode(trim($email)));
        $this->config = container('config')->email;

        if ($sanitize) {
            $this->sanitize();
        }
    }

    /**
     * Tests whether a string contains only 7-bit ASCII bytes.
     * This is used to determine when to use native functions or UTF-functions.
     *
     * <code>
     * var_dump($email->isAscii($string));
     * </code>
     *
     * @param  string $text String or array of strings to check
     * @return bool
     */
    public function isAscii($text)
    {
        return !preg_match('/[^\x00-\x7F]/S', $text);
    }

    /**
     * Validate email address
     *
     * @return bool
     */
    public function valid()
    {
        $multipleValidations = new MultipleValidationWithAnd([
            new LengthValidator(),
            new RequiredTokensValidator(),
            new AppValidator($this->config->appParts->toArray()),
            new CorpValidator($this->config->corpParts->toArray()),
            new RoleValidator($this->config->roleParts->toArray()),
            new RFCValidation(),
            new DNSCheckValidation(),
        ]);

        $validator = new EmailValidator();
        $result = $validator->isValid($this->email, $multipleValidations);

        return $result;
    }

    /**
     * Sanitize the email address.
     *
     * @return $this
     */
    protected function sanitize()
    {
        // str_replace works just fine with multibyte strings
        $this->email = str_replace($this->config->badChars->toArray(), '', $this->email);
        $this->email = preg_replace('(\.[.]+)', '.', $this->email);

        if (false === mb_strpos($this->email, '@')) {
            $this->fixAt();

            // false or 0 - error
            if (!mb_strpos($this->email, '@')) {
                return $this;
            }
        }

        $domainPart = mb_strrchr($this->email, '@');
        $namePart   = mb_substr($this->email, 0, mb_strlen($this->email) - mb_strlen($domainPart));
        $namePart   = trim($namePart, '.@');
        $firstChar  = mb_substr($namePart, 0, 1);

        // Clean name
        if (preg_match('/^[а-я]/iu', $namePart) &&
            preg_match('/.[^а-я]/iu', $namePart) &&
            isset($this->config->offsetGet('incorrectFirstChar')[$firstChar])
        ) {
            $namePart[0] = $this->config->offsetGet('incorrectFirstChar')[$firstChar];
        }

        if (!$this->isAscii($namePart)) {
            $namePart = str_replace(
                array_keys($this->config->utfLower->toArray()),
                array_values($this->config->utfLower->toArray()),
                $namePart
            );
        }

        $domainPart = ltrim($domainPart, '-.@');

        // isset is faster than array_key_exists also because is a language construct, not a function
        if (isset($this->config->incorrectDomains[$domainPart])) {
            $domainPart = $this->config->incorrectDomains[$domainPart];
        }

        $parts = explode('.', $domainPart);
        $last  = array_pop($parts);

        if (isset($this->config->incorrectFirstLevelDomains[$last])) {
            $parts[] = $this->config->incorrectFirstLevelDomains[$last];
            $domainPart = implode('.', $parts);
        }

        // Clean domain
        $domainPart = $this->cleanDomainPart($domainPart);

        $this->email = $namePart . '@' . $domainPart;
        $this->email = filter_var($this->email, FILTER_SANITIZE_EMAIL);

        return $this;
    }

    /**
     * Clean domain part.
     *
     * @param  string $domainPart
     * @return string
     */
    protected function cleanDomainPart($domainPart)
    {
        $domainPart = preg_replace('#^(\w+)(\.)(кг|ry|к|pu|r|rui)$#ui', '$1$2ru', $domainPart);
        $domainPart = preg_replace('#^(\w+)(\.)(c)$#ui', '$1$2com', $domainPart);
        $domainPart = preg_replace('#^(\w+)(\.)(n|ne)$#ui', '$1$2net', $domainPart);
        $domainPart = preg_replace('#^(yandex(?:ru|\.|))?$#ui', 'yandex.ru', $domainPart);
        $domainPart = preg_replace(
            '#^(facebook|hotmail|outlook|gmail|yahoo|live|yandex|icloud|mail)(\.)(co|c|)$#ui',
            '$1$2com',
            $domainPart
        );
        $domainPart = preg_replace('#^(ma[ij]l(?:ru|\.|))$#ui', 'mail.ru', $domainPart);
        $domainPart = preg_replace('#^([a-z][a-z1-9]+)-ru$#ui', '$1.ru', $domainPart);
        $domainPart = preg_replace('#^(majl\.ru)$#ui', 'mail.ru', $domainPart);
        $domainPart = preg_replace('#[^-\da-z.]#ui', '', $domainPart);

        return $domainPart;
    }

    /**
     * Fix missed @ fo known domains.
     */
    protected function fixAt()
    {
        foreach ($this->config->atDomains->toArray() as $domain) {
            $domainPart = mb_strrchr($this->email, $domain);

            if ($domainPart == $domain) {
                $namePart = mb_substr($this->email, 0, mb_strlen($this->email) - mb_strlen($domainPart));
                $this->email = $namePart . '@' . $domainPart;
                return;
            }
        }
    }
}
