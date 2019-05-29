<?php

namespace APp\GraphQL\Traits;

trait Authable
{
	public function authorize(array $args)
	{
		return !Auth::guest();
	}
}
