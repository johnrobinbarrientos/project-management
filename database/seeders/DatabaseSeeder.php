<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        DB::table('users')->insert([
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'firstname' => 'Kenji',
            'lastname' => 'Test',
            'phone' => '09177012123',
            'status' => 'verified',
            'email' => 'kenjimagto@gmail.com',
            'password' => Hash::make('secret123'),
        ]);
    }
}
