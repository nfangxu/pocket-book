<?php

use Illuminate\Database\Seeder;
use App\Models\Pocket;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);

        factory(Pocket::class, 20)->create();
    }
}
