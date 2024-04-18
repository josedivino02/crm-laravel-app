<?php

namespace Database\Seeders;

use App\Models\Opportunity;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opps = [];

        for ($i = 0; $i <= 300; $i++) {
            $opps[] = Opportunity::factory(300)
                ->make([
                    'customer_id' => rand(1, 70),
                    // 'customer_id' => Customer::query()->inRandomOrder()->first(['id'])->id,
                ])->toArray();
        }

        Opportunity::query()->insert($opps);
    }
}