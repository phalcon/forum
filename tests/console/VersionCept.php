<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new ConsoleTester($scenario);

$I->wantToTest('getting version');

$I->amInPath(dirname(app_path()));
$sha = substr(trim($I->runShellCommand('git rev-parse HEAD')), 0, 7);

$output=<<<OUT
Phosphorum version 3.2.1, git commit $sha
OUT;

$I->runShellCommand('php forum -V');
$I->seeInShellOutput($output);

$I->runShellCommand('php forum --version');
$I->seeInShellOutput($output);
