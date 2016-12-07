<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new ConsoleTester($scenario);

$I->wantToTest('clear filesystem cache');

$I->haveFile(cache_path('data/cached.php'));
$I->amInPath(cache_path('data'));
$I->seeFileFound('cached.php');

$I->amInPath(dirname(app_path()));
$I->runShellCommand('php forum cache:clear');

$output=<<<OUT
Start
Clear file cache...
Done
OUT;

$I->seeInShellOutput($output);

$I->amInPath(cache_path('data'));
$I->dontSeeFileFound('cached.php');
