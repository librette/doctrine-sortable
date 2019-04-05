<?php
if (!$loader = include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}
$loader->addPsr4('LibretteTests\\Doctrine\\Sortable\\', __DIR__ . '/src');

\Tracy\Debugger::enable(\Tracy\Debugger::DEVELOPMENT, __DIR__ . '/tmp/');
Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

define('TEMP_DIR', __DIR__ . '/tmp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
define('VENDOR_DIR', __DIR__ . '/../vendor/');
Tester\Helpers::purge(TEMP_DIR);

$_SERVER = array_intersect_key($_SERVER, array_flip(array('PHP_SELF', 'SCRIPT_NAME', 'SERVER_ADDR', 'SERVER_SOFTWARE', 'HTTP_HOST', 'DOCUMENT_ROOT', 'OS', 'argc', 'argv')));
$_SERVER['REQUEST_TIME'] = 1234567890;
$_ENV = $_GET = $_POST = array();

function run(Tester\TestCase $testCase)
{
	if (isset($_SERVER['argv'][2])) {
		$testCase->runTest($_SERVER['argv'][2]);
	} else {
		$testCase->run();
	}
}
