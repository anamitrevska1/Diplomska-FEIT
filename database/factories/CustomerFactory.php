<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by'=>1,
            'customer_type'=>'Business',
            'first_name'=>fake()->firstName(),
            'last_name'=>fake()->lastName(),
            'company_name'=>fake()->company(),
            'email'=>fake()->safeEmail(),
            'phone'=>fake()->phoneNumber(),
            'address'=>fake()->address(),
            'city'=>fake()->city(),
            'zip' => fake()->randomDigit(),
            'prev_cutoff_Date'=>fake()->date(),
            'prev_bill_date'=>fake()->date(),
            'next_bill_date'=>fake()->date(),
            'bill_period' => 1,
            'no_bill' =>0
        ];
    }
}
