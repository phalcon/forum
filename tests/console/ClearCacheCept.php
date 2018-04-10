<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new ConsoleTester($scenario);

$I->wantToTest('clear application cache');

container('modelsCache')->save('some-model-key', 'some-model-content');
container('viewCache')->save('some-view-key', 'some-view-content');
$basePath = dirname(app_path());

$I->haveFile(cache_path('data/cached.php'));
$I->amInPath(cache_path('data'));
$I->seeFileFound('cached.php');

$I->assertSame('some-model-content', container('modelsCache')->get('some-model-key'));
$I->assertSame('some-view-content', container('viewCache')->get('some-view-key'));
$I->copyDir($basePath . '/tests/_data/assets/', $basePath. '/public/assets/');

$I->seeFileFound($basePath. '/public/assets/script.js');
$I->seeFileFound($basePath. '/public/assets/style.css');

$I->amInPath($basePath);
$I->runShellCommand('php forum cache:clear');

$output=<<<OUT
Start
Clear file cache...
Clear models cache...
Clear view cache...
Clear annotations cache...
Clear assets collections files...
Done
OUT;

$I->seeInShellOutput($output);

$I->amInPath(cache_path('data'));
$I->dontSeeFileFound('cached.php');
$I->dontSeeFileFound($basePath . '/public/assets/script.js');
$I->dontSeeFileFound($basePath . '/public/assets/style.css');

$I->assertNull(container('modelsCache')->get('some-model-key'));
$I->assertNull(container('viewCache')->get('some-view-key'));
