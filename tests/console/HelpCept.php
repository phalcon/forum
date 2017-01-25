<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new ConsoleTester($scenario);

$I->wantToTest('getting help from command line');

$output=<<<OUT
Phosphorum 3.2.1
Usage: php forum [command <arguments>] [--help | -H] [--version | -V] [--list]
OUT;

$I->amInPath(dirname(app_path()));

$I->runShellCommand('php forum');
$I->seeInShellOutput($output);

$I->runShellCommand('php forum help');
$I->seeInShellOutput($output);

$I->runShellCommand('php forum help:main');
$I->seeInShellOutput($output);

$I->runShellCommand('php forum -H');
$I->seeInShellOutput($output);

$I->runShellCommand('php forum --help');
$I->seeInShellOutput($output);
