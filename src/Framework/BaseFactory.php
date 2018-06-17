<?php declare(strict_types=1);

namespace App\Framework;

use Base;

final class BaseFactory
{
	public function create(): Base
	{
		return Base::instance();
	}
}
