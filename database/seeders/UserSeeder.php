<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@inventory.test',
            'password' => Hash::make('password'),
            'role'     => Role::Admin->value,
        ]);

        User::create([
            'name'     => 'Staff',
            'email'    => 'staff@inventory.test',
            'password' => Hash::make('password'),
            'role'     => Role::Staff->value,
        ]);

        User::create([
            'name'     => 'Kepala Gudang',
            'email'    => 'kepala@inventory.test',
            'password' => Hash::make('password'),
            'role'     => Role::KepalaGudang->value,
        ]);

        User::create([
            'name'     => 'Management',
            'email'    => 'management@inventory.test',
            'password' => Hash::make('password'),
            'role'     => Role::Management->value,
        ]);
    }
}
