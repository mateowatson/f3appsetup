<?php declare(strict_types=1);

namespace App\Models;

use Base;
use DB\SQL;
use DB\SQL\Mapper;

final class Projects extends Model
{
	private $user;
	
	public function __construct(SQL $db) {
		parent::__construct($db);
		$this->user = new Mapper($db, 'projects');
	}
}
