<?php

namespace App\GraphQL\Mutation;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use App\User;
use GraphQL;
use Illuminate\Support\Facades\Auth;

class SignUp extends Mutation
{
    protected $attributes = [
        'name' => 'SignUp',
        'description' => '用户注册'
    ];

    public function type()
    {
        return GraphQL::type('user');
    }

    public function args()
    {
        return [
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
                'rules' => ['required', 'email']
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::string(),
                'rules' => ['required', 'min:6', 'confirmed']
            ],
            'password_confirmation' => [
                'name' => 'password_confirmation',
                'type' => Type::string(),
            ],
        ];
    }

    public function validationErrorMessages($args = [])
    {
        return [
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式错误',
            'password.required' => '密码不能为空',
            'password.min' => '密码至少为 6 位',
            'password.confirmed' => '两次密码不一致'
        ];
    }

    public function resolve($root, $args, SelectFields $fields, ResolveInfo $info)
    {
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $user = User::create([
            'email' => $args['email'],
            'password' => $args['password'],
        ]);

        Auth::login($user);

        return $user;
    }
}
