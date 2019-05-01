<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new ConsoleTester($scenario);

$I->wantToTest('getting tasks list');

$I->amInPath(dirname(app_path()));

$I->runShellCommand('php forum --list');
