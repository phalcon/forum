<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new AcceptanceTester($scenario);
$I->wantTo('make sure that the robots.txt is exists');

$I->sendGET('/robots.txt');
$I->seeResponseCodeIs(200);

$pattern = '#^User-agent: \*\nAllow: \/\nSitemap: https?:\/\/.+\/sitemap$#';
$I->seeResponseRegexp($pattern, $I->grabResponse());
