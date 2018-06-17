<?php declare(strict_types=1);

namespace App\Controllers;

final class HomepageController extends Controller
{
	public function read(array $params) {
		$this->f3->set('pagename', 'Homepage');
		echo $this->view->render('home.htm');
	}
}
