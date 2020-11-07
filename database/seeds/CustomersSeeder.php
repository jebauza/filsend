<?php

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = factory(Customer::class, 10)->create([
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
