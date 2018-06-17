<?php declare(strict_types=1);

namespace App\Models;

use Base;
use DB\SQL;
use DB\SQL\Mapper;

final class Users extends Model
{
	private $user;
	
	public function __construct(SQL $db) {
		parent::__construct($db);
		$this->user = new Mapper($db, 'users');
	}

	public function getByEmail($email) {
		$this->user->load(array('email=?',$email));
        return $this->user;
	}
}
