<?php declare(strict_types=1);

use Auryn\Injector;
use App\Framework\BaseFactory;
use App\Framework\DatabaseConnectionFactory;
use DB\SQL;

$injector = new Injector();

$injector->share(Base::class);
$injector->delegate(Base::class, function () use ($injector): Base {
	$factory = $injector->make(BaseFactory::class);
	return $factory->create();
});

$injector->share(SQL::class);
$injector->delegate(SQL::class, function () use ($injector): SQL {
	$factory = $injector->make(DatabaseConnectionFactory::class);
	return $factory->create();
});

return $injector;
