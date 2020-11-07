<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::updateOrCreate(
            ['email' => 'jebauza@gmail.com'],
            [
                'firstname' => 'Jorge',
                'secondname' => 'Ernesto',
                'lastname' => 'Bauza Becerra',
                'username' => 'jebauza',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'created_by' => 1,
                'updated_by' => 1,
            ]
        );
        factory(User::class, 20)->create();
    }
}
