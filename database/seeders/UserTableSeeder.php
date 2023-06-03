<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User::create([
        //     'name' => 'Kuswandi',
        //     'email' => 'kuswandi.ti@gmail.com',
        //     'password' => bcrypt('rahasia'),
        // ]);

        User::factory(50)->create();
    }
}
