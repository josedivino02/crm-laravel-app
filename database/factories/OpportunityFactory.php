<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Traits\Factory\HasDeleted;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpportunityFactory extends Factory
{
    use HasDeleted;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'title'       => $this->faker->sentence(),
            'status'      => $this->faker->randomElement(['open', 'won', 'lost']),
            'title'       => $this->faker->numberBetween(1000, 100000),
        ];
    }
}
