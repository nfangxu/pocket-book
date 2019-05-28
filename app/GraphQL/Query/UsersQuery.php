<?php

namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Query;
use GraphQL;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class UsersQuery extends Query
{
    protected $attributes = [
        'name' => 'Users',
        'description' => '用户列表'
    ];
    
    public function type()
    {
        return GraphQL::paginate('user');
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'email' => ['name' => 'email', 'type' => Type::string()],
            'page' => ['name' => 'page',  'type' => Type::int()],
            'limit' => ['name' => 'limit',  'type' => Type::int()],
        ];
    }

    public function resolve($root, $args, SelectFields $fields, ResolveInfo $info)
    {
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $query = User::query();

        if ($select) {
            $query->select(Arr::wrap($select));
        }

        if ($with) {
            $query->with(Arr::wrap($with));
        }

        return $query->paginate(
            $args['limit'] ?? 10,
            Arr::wrap($select ?: "*"),
            null,
            $args['page'] ?? 1
        );
    }
}
