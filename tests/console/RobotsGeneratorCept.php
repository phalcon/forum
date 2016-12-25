<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new ConsoleTester($scenario);

$I->wantToTest('generating the robots files');

$output=<<<OUT
The robots.txt was successfully updated
OUT;

$I->amInPath(dirname(app_path()));

$I->haveFile('public/robots.txt');
$I->deleteFile('public/robots.txt');
$I->dontSeeFileFound('public/robots.txt');

$I->runShellCommand('php forum robots:generate');
$I->seeInShellOutput($output);
$I->seeFileFound('public/robots.txt');

$I->runShellCommand('php forum robots:generate');
$I->seeInShellOutput($output);
$I->seeFileFound('public/robots.txt');
