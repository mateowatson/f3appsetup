<?php declare(strict_types=1);

namespace App\Controllers;

use Base;
use DB\SQL\Session;
use App\Models\Users;
use App\Models\Projects;

class Controller
{
	protected $f3;
	protected $view;
	protected $users;
	protected $session;
	protected $projects;

	public function __construct(
		Base $base,
		Session $session,
		Users $users,
		Projects $projects
	) {
		$this->f3 = $base;
		$this->session = $session;
		$this->view = \View::instance();
		$this->users = $users;
		$this->projects = $projects;
	}
}
