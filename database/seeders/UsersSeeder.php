<?php

namespace Database\Seeders;

use App\Models\{Can, User};
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
    }
}
