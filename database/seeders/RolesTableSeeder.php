<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = array(
            [
                'title' => 'Студент',
                'slug' => 'student',
                'description' => null
            ],
            [
                'title' => 'Преподаватель',
                'slug' => 'teacher',
                'description' => null
            ],
            [
                'title' => 'Бизнес',
                'slug' => 'business',
                'description' => null
            ]
        );

        foreach ($roles as $role) {
            Role::updateOrCreate([
                'slug' => $role['slug'],
            ], $role);
        }
    }
}
