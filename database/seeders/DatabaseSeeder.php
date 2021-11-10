<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
             /**
         * Seed Users Table
         */
        DB::table('users')->insert([
            [
                'name' =>  'admin',
                'email' =>  'admin@gmail.com',
                'user_name' =>  'admin',
                'password' => Hash::make('admin'),
                'user_role' => 'admin',
                'created_at' => Carbon::now(),

            ],
            [
                'name' =>  'John Doe',
                'email' =>  'johndoe@example.com',
                'user_name' =>  'johndoe',
                'password' => Hash::make('johndoe'),
                'user_role' => 'user',
                'created_at' => Carbon::now(),

            ],
        ]);

    }
}
