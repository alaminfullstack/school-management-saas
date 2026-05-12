<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        User::updateOrCreate(
            ['email' => 'asthaitlimited@gmail.com'],
            [
                'role' => 'admin',
                'name' => 'Super Admin',
                'school_name' => null,
                'mobile' => '01942845813',
                'id_number' => 'ADMIN001',
                'password' => Hash::make('12345678admin'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // /*
        // |--------------------------------------------------------------------------
        // | SCHOOL
        // |--------------------------------------------------------------------------
        // */
        // User::updateOrCreate(
        //     ['email' => 'school@test.com'],
        //     [
        //         'role' => 'school',
        //         'name' => 'Green Valley School',
        //         'school_name' => 'Green Valley School',
        //         'mobile' => '2222222222',
        //         'id_number' => 'SCHOOL001',
        //         'password' => Hash::make('123456'),
        //     ]
        // );

        // /*
        // |--------------------------------------------------------------------------
        // | TEACHER
        // |--------------------------------------------------------------------------
        // */
        // User::updateOrCreate(
        //     ['email' => 'teacher@test.com'],
        //     [
        //         'role' => 'teacher',
        //         'name' => 'John Teacher',
        //         'school_name' => 'Green Valley School',
        //         'mobile' => '3333333333',
        //         'id_number' => 'TEACHER001',
        //         'password' => Hash::make('123456'),
        //     ]
        // );

        // /*
        // |--------------------------------------------------------------------------
        // | STUDENT
        // |--------------------------------------------------------------------------
        // */
        // User::updateOrCreate(
        //     ['email' => 'student@test.com'],
        //     [
        //         'role' => 'student',
        //         'name' => 'Rahim Student',
        //         'school_name' => 'Green Valley School',
        //         'mobile' => '4444444444',
        //         'id_number' => 'STUDENT001',
        //         'password' => Hash::make('123456'),
        //     ]
        // );
    }
}
