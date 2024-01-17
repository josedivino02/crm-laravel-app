<?php

namespace Database\Seeders;

use App\Enum\Can;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->withPermission(Can::BE_AN_ADMIN)
            ->create([
                'name'  => 'Divino',
                'email' => 'jose@divino.com',
            ]);

        User::factory()->count(50)->create();
        User::factory()->count(10)->deleted()->create();
    }
}
