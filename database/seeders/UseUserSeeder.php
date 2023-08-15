<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UseUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            [
                'email' => 'artem.mikheev.git@gmail.com',
                'password' => bcrypt('12345'),
                'license_agreement' => true,
                'email_verified_at' => new \DateTime(),
                'phone_verified_at' => new \DateTime(),
            ]
        );

        foreach ($data as $item) {
            $param = new User($item);
            $param->save();
        }
    }
}
