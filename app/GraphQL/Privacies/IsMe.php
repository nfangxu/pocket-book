<?php

namespace App\GraphQL\Privacies;

use Rebing\GraphQL\Support\Privacy;
use Illuminate\Support\Facades\Auth;

class IsMe extends Privacy
{
	public function validate(array $args)
	{
		if (isset($args['id'])) {
			return $args['id'] == Auth::guard('web')->id();
		}

		return true;
	}
}
