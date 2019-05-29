<?php

namespace App\GraphQL\Mutation;

use GraphQL;
use Illuminate\Support\Arr;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use App\User;

class LoginMutation extends Mutation
{
    protected $attributes = [
        'name'          => 'Login',
        'description'   => 'Log the user in by email',
    ];

    public function type()
    {
        return GraphQL::type('user');
    }

    public function args()
    {
        return [
            'email' => [
                'name'  => 'email',
                'type'  => Type::nonNull(Type::string()),
                'rules' => ['required', 'email'],
            ],
            'password' => [
                'name'  => 'password',
                'type'  => Type::nonNull(Type::string()),
                'rules' => ['required', 'string'],
            ],
            'remember_me' => [
                'name'  => 'remember_me',
                'type'  => Type::boolean(),
                'rules' => ['boolean'],
            ],
        ];
    }

    public function resolve($root, $args)
    {

        if (Auth::attempt(['email' => $args['email'], 'password' => $args['password']], $args['remember_me'] ?? false)) {
            return User::first();
        } else {
            return null;
         }
    }
}
