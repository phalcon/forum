<?php
/**
 * @var Codeception\Scenario $scenario
 */

// We have rewrite rule for Nginx
// robots.txt => robots

$I = new AcceptanceTester($scenario);
$I->wantTo('make sure that the robots.txt is exists');

$I->sendGET('/robots');
$I->seeResponseCodeIs(200);

$pattern = '#^User-agent: \*\nAllow: \/\nSitemap: https?:\/\/.+\/sitemap$#';
$I->seeResponseRegexp($pattern, $I->grabResponse());
