<?php

use Codeception\Test\Unit;

class SlugTest extends Unit
{
    /**
     * UnitTester Object
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Phosphorum\Utils\Slug
     */
    protected $slug;

    /**
     * Executed before each test
     */
    protected function _before()
    {
        $this->slug = $this->tester->getDi()->getShared('slug');
    }

    /**
     * Executed after each test
     */
    protected function _after()
    {
    }

    public function nonAsciiStringProvider()
    {
        return [
            ["Phålcón", 'phalcon'],
            ["I ♥ Phålcón\021", 'i  phalcon'],
            ["\x7FI ♥ Phalcon", 'i  phalcon'],
            ['фалькон', 'falkon'],
            ["Perchè l'erba è verde?", "perche lerba e verde"],
            [
                "Mess'd up --text-- just (to) stress/test/ ?our! `little` \\clean\\ url fun.ction!?-->",
                'messd up text just to stresstest our little clean url function'
            ],
            [
                'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝßàáâãäåæçèéêëìíîïñòóôõöùúûüýÿ',
                'aaaaaaaeceeeeiiiinooooouuuuyssaaaaaaaeceeeeiiiinooooouuuuyy'
            ],
            ['Я люблю Фалькон', 'a lublu falkon']
        ];
    }

    public function validUrlProvider()
    {
        return [
            ["Phålcón", 'phalcon'],
            ["I ♥ Phålcón\021", 'i-phalcon'],
            ["\x7FI ♥ Phalcon", 'i-phalcon'],
            ['фалькон', 'falkon'],
            ['a b c d e f g h', 'a-b-c-d-e-f-g-h'],
            [
                "Mess'd up --text-- just (to) stress/test/ ?our! `little` \\clean\\ url fun.ction!?-->",
                'messd-up-text-just-to-stresstest-our-little-clean-url-function'
            ],
            ["Perchè l'erba è verde?", 'perche-lerba-e-verde'],
            [
                'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝßàáâãäåæçèéêëìíîïñòóôõöùúûüýÿ',
                'aaaaaaaeceeeeiiiinooooouuuuyssaaaaaaaeceeeeiiiinooooouuuuyy'
            ],
        ];
    }

    /**
     * @dataProvider nonAsciiStringProvider
     * @param string $input
     * @param string $expected
     */
    public function testShouldTransliterateViaCharMap($input, $expected)
    {
        if (!extension_loaded('mbstring') || !MB_OVERLOAD_STRING) {
            $this->markTestSkipped('Slug::transliterateViaCharMap requires mbstring for overload string functions');
        }

        $reflectionMethod = new ReflectionMethod($this->slug, 'transliterateViaCharMap');
        $reflectionMethod->setAccessible(true);
        $this->assertEquals($expected, $reflectionMethod->invoke($this->slug, $input));
    }

    /**
     * @dataProvider nonAsciiStringProvider
     * @param string $input
     * @param string $expected
     */
    public function testShouldTransliterateViaIntl($input, $expected)
    {
        if (!extension_loaded('iconv')) {
            $this->markTestSkipped('Slug::transliterateViaIntl requires php-intl extension');
        }

        $reflectionMethod = new ReflectionMethod($this->slug, 'transliterateViaIntl');
        $reflectionMethod->setAccessible(true);
        $this->assertEquals($expected, $reflectionMethod->invoke($this->slug, $input));
    }

    /**
     * @dataProvider nonAsciiStringProvider
     * @param string $input
     * @param string $expected
     */
    public function testShouldReturnSameResultForAnyMethod($input, $expected)
    {
        if (!extension_loaded('iconv')) {
            $this->markTestSkipped('Slug::transliterateViaIntl requires php-intl extension');
        }

        if (!extension_loaded('mbstring') || !MB_OVERLOAD_STRING) {
            $this->markTestSkipped('Slug::transliterateViaCharMap requires mbstring for overload string functions');
        }

        $transliterateViaCharMap = new ReflectionMethod($this->slug, 'transliterateViaCharMap');
        $transliterateViaCharMap->setAccessible(true);

        $transliterateViaIntl = new ReflectionMethod($this->slug, 'transliterateViaIntl');
        $transliterateViaIntl->setAccessible(true);

        $this->assertEquals($transliterateViaCharMap->invoke($this->slug, $input), $transliterateViaIntl->invoke($this->slug, $input));
    }

    /**
     * @dataProvider validUrlProvider
     * @param string $input
     * @param string $expected
     */
    public function testShouldConvertNonAsciiStringToValidUrl($input, $expected)
    {
        $this->assertEquals($expected, $this->slug->generate($input));
    }
}
