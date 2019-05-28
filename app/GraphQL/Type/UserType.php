<?php

namespace App\GraphQL\Type;

use Rebing\GraphQL\Support\Type as GraphQLType;
use App\User;
use GraphQL\Type\Definition\Type;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'A type',
        'model' => User::class,
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => '用户 ID',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => '用户名称',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => '用户邮箱',
            ],
        ];
    }

    /** 类似 Orm 的修改器 */
    protected function resolveEmailField($root, $args)
    {
        return strtolower($root->email);
    }
}