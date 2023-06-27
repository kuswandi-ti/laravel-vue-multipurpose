<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            [
                'key' => 'app_name',
                'value' => 'Test App'
            ],
            [
                'key' => 'date_format',
                'value' => 'DD/MM/YYYY'
            ],
            [
                'key' => 'pagination_limit',
                'value' => 5
            ],
        ]);
    }
}
