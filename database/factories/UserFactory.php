<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'role'              => Role::Staff->value,
            'remember_token'    => Str::random(10),
        ];
    }

    /** Buat user dengan role Admin */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Admin->value,
        ]);
    }

    /** Buat user dengan role Staff */
    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Staff->value,
        ]);
    }

    /** Buat user dengan role Kepala Gudang */
    public function kepalaGudang(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::KepalaGudang->value,
        ]);
    }

    /** Buat user dengan role Management */
    public function management(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Management->value,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
