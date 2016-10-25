<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new AcceptanceTester($scenario);

$I->wantTo('make sure that every page in the sitemap');
$I->sendGET('/sitemap.xml');
$I->seeResponseCodeIs(200);

$I->seeResponseIsXml();
$urls = $I->parseSitemap($I->grabResponse());

foreach ($urls as $url) {
    $I->amOnUrl($url);
    $I->seeResponseCodeIs(200);
}
