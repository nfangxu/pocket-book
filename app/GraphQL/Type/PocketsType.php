<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use App\Models\Pocket;

class PocketsType extends GraphQLType
{
	protected $attributes = [
		'name' => 'pockets',
		'description' => '花销记录',
		'model' => Pocket::class
	];

	public function fields()
	{
		return [
			'id' => [
				'type' => Type::nonNull(Type::int()),
				'description' => '用户id'
			],
			'name' => [
				'type' => Type::string(),
				'description' => '用户名'
			],
			'email' => [
				'type' => Type::string(),
				'description' => '邮箱'
			],
		];
	}
}
