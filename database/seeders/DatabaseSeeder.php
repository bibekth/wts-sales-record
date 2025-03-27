<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::insert([
            ['id'=>1,'role'=>'Admin'],
            ['id'=>2,'role'=>'Client'],
            ['id'=>3,'role'=>'User'],
        ]);

        User::insert([
            [
                'name'=>'admin user',
                'email'=>'admin@gmail.com',
                'password'=>Hash::make('wts12345'),
                'role_id'=>1
            ],
            [
                'name'=>'client user',
                'email'=>'client@gmail.com',
                'password'=>Hash::make('wts12345'),
                'role_id'=>2
            ],
            [
                'name'=>'user user',
                'email'=>'user@gmail.com',
                'password'=>Hash::make('wts12345'),
                'role_id'=>3
            ],
        ]);
    }
}
