<?php
/**
 * @var Codeception\Scenario $scenario
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

codecept_debug($uri);
codecept_debug($_SERVER);

$I = new AcceptanceTester($scenario);
$I->wantTo('make sure that every page in the sitemap');

$I->sendGET('/sitemap.xml');


codecept_debug($I->grabResponse());


libxml_use_internal_errors(true);
$doc = simplexml_load_string($I->grabResponse());
$num = "";
$title = "";
$error = null;
if ($doc === false) {
    $error = libxml_get_last_error();
    $num = $error->code;
    $title = trim($error->message);
    libxml_clear_errors();
}
libxml_use_internal_errors(false);


codecept_debug([
    'error' => $error,
    'num'   => $num,
    'title' => $title,
]);

//$I->seeResponseIsXml();
/*$urls = $I->parseSitemap($I->grabResponse());

foreach ($urls as $url) {
    $I->amOnUrl($url);
    $I->seeResponseCodeIs(200);
}*/
