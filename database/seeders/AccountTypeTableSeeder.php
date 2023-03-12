<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeTableSeeder extends Seeder
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
                'title' => 'Студенческий',
                'code' => AccountType::STUDENT_CODE,
                'description' => 'Тип аккаунта предоставляет возможность подписываться на курсы'
            ],
            [
                'title' => 'Преподаватель',
                'code' => AccountType::TEACHER_CODE,
                'description' => "Тип аккаунта позволяет стать преподавателем на курсах"
            ],
            [
                'title' => 'Бизнес',
                'code' => AccountType::BUSINESS_CODE,
                'description' => 'Тип аккаунта позволяет создавать свою школу и курсы, приглашать учителей и студентов на курсы'
            ]
        );

        foreach ($data as $item) {
            AccountType::updateOrCreate([
                'code' => $item['code'],
            ], $item);
        }
    }
}
