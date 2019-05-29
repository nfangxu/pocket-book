<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GraphQL;
use App\GraphQL\Type\UserType;
use App\GraphQL\Query\UsersQuery;
use App\GraphQL\Mutation\SignUp;

class GraphQLServiceProvider extends ServiceProvider
{
    protected $types = [
        'user' => UserType::class,
    ];

    protected $queries = [
        'users' => UsersQuery::class,
    ];

    protected $mutations = [
        'signUp' => SignUp::class,
    ];

    public function register()
    {
        // 
    }

    public function boot()
    {
        GraphQL::addTypes($this->types);
        GraphQL::addSchema('default', [
            'query' => $this->queries,
            'mutation' => $this->mutations,
        ]);
    }
}
