<?php declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));

require ROOT_DIR . '/vendor/autoload.php';


$f3 = \Base::instance();
$f3->set('UI', ROOT_DIR . '/templates/');

$routes = include(ROOT_DIR . '/src/Routes.php');

$injector = include('Dependencies.php');

foreach ($routes as $route) {
	$f3->route($route[0], function ($f3, $params) use ($injector, $route) {
		$controller = $injector->make($route[1]);
		$method = $route[2];
		$controller->$method($params);
	});
}

$f3->run();
