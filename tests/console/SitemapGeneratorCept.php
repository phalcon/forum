<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new ConsoleTester($scenario);

$I->wantToTest('generating the sitemap');

$output=<<<OUT
The sitemap.xml was successfully updated
OUT;

$I->amInPath(dirname(app_path()));

$I->haveFile('public/sitemap.xml');
$I->deleteFile('public/sitemap.xml');
$I->dontSeeFileFound('public/sitemap.xml');

$I->runShellCommand('php forum sitemap:generate');
$I->seeInShellOutput($output);
$I->seeFileFound('public/sitemap.xml');

$I->runShellCommand('php forum sitemap:generate');
$I->seeInShellOutput($output);
$I->seeFileFound('public/sitemap.xml');
