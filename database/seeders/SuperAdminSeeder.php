<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("INSERT INTO users (name, email, password) VALUES ('Super Admin', 'superadmin@gmail.com', '".bcrypt('password')."')");
		DB::statement("INSERT INTO role_user (user_id, role_id) VALUES ((SELECT id FROM users WHERE email='superadmin@gmail.com'),(SELECT id FROM roles WHERE name='SuperAdmin'))");
    }
}
