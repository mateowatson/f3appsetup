<?php declare(strict_types=1);

namespace App\Framework;

use DB\SQL;

final class DatabaseConnectionFactory
{
	public function create(): SQL
	{
		return new SQL(
			'mysql:host=localhost;port=3306;dbname=adagio4',
			'root',
			''
		);;
	}
}
