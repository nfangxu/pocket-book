<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(["name" => "nfangxu", "email" => "nfangxu@gmail.com", 'password' => bcrypt(str_random(8))]);
        User::create(["name" => "aling", "email" => "zll952700@gmail.com", 'password' => bcrypt(str_random(8))]);
    }
}
