<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement("TRUNCATE TABLE users");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        User::create([
            'email' => "admin@yopmail.com",
            'password' => bcrypt("admin@123"),
            'status' => ActiveStatus::ACTIVE,
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
