<?php declare(strict_types=1);

namespace App\Models;

use Base;
use DB\SQL;

class Model
{
	protected $db;

	public function __construct(SQL $db) {
		$this->db = $db;
	}
}
