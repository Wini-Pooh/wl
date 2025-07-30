<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeFinance;
use App\Models\User;
use App\Models\Project;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем партнера (пользователя с ролью partner)
        $partner = User::whereHas('roles', function($q) {
            $q->where('name', 'partner');
        })->first();

        if (!$partner) {
            // Создаем тестового партнера, если его нет
            $partner = User::create([
                'name' => 'Тестовый Партнер',
                'email' => 'partner@test.com',
                'phone' => '+7 (999) 123-45-67',
                'password' => bcrypt('password'),
                'default_role_id' => 2, // предполагаем, что ID роли partner = 2
            ]);
        }

        // Создаем тестовых сотрудников
        $employees = [
            [
                'first_name' => 'Иван',
                'last_name' => 'Петров',
                'middle_name' => 'Сергеевич',
                'phone' => '+7 (999) 111-22-33',
                'email' => 'ivan.petrov@test.com',
                'role' => 'foreman',
                'status' => 'active',
                'description' => 'Ответственный прораб с опытом работы 5 лет',
                'partner_id' => $partner->id,
            ],
            [
                'first_name' => 'Алексей',
                'last_name' => 'Сидоров',
                'middle_name' => 'Владимирович',
                'phone' => '+7 (999) 444-55-66',
                'email' => 'alexey.sidorov@test.com',
                'role' => 'subcontractor',
                'status' => 'active',
                'description' => 'Субподрядчик по электромонтажным работам',
                'partner_id' => $partner->id,
            ],
            [
                'first_name' => 'Мария',
                'last_name' => 'Козлова',
                'middle_name' => 'Андреевна',
                'phone' => '+7 (999) 777-88-99',
                'email' => 'maria.kozlova@test.com',
                'role' => 'estimator',
                'status' => 'active',
                'description' => 'Сметчик с опытом работы в строительстве',
                'partner_id' => $partner->id,
            ],
            [
                'first_name' => 'Дмитрий',
                'last_name' => 'Николаев',
                'middle_name' => 'Игоревич',
                'phone' => '+7 (999) 000-11-22',
                'email' => null,
                'role' => 'subcontractor',
                'status' => 'inactive',
                'description' => 'Временно неактивный субподрядчик',
                'partner_id' => $partner->id,
            ],
        ];

        foreach ($employees as $employeeData) {
            $employee = Employee::create($employeeData);

            // Создаем финансовые записи для каждого сотрудника
            $this->createFinancialRecords($employee);
        }
    }

    /**
     * Создает финансовые записи для сотрудника
     */
    private function createFinancialRecords(Employee $employee)
    {
        $finances = [];

        // Зарплата за прошлый месяц (выплачена)
        $finances[] = [
            'employee_id' => $employee->id,
            'type' => 'salary',
            'amount' => rand(50000, 80000),
            'currency' => 'RUB',
            'title' => 'Зарплата за ' . Carbon::now()->subMonth()->format('m.Y'),
            'description' => 'Ежемесячная заработная плата',
            'status' => 'paid',
            'due_date' => Carbon::now()->subMonth()->endOfMonth(),
            'paid_date' => Carbon::now()->subMonth()->endOfMonth()->addDays(5),
            'created_at' => Carbon::now()->subMonth(),
            'updated_at' => Carbon::now()->subMonth()->endOfMonth()->addDays(5),
        ];

        // Зарплата за текущий месяц (ожидает выплаты)
        $finances[] = [
            'employee_id' => $employee->id,
            'type' => 'salary',
            'amount' => rand(50000, 80000),
            'currency' => 'RUB',
            'title' => 'Зарплата за ' . Carbon::now()->format('m.Y'),
            'description' => 'Ежемесячная заработная плата',
            'status' => 'pending',
            'due_date' => Carbon::now()->endOfMonth(),
            'paid_date' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        // Премия (если активный сотрудник)
        if ($employee->status === 'active') {
            $finances[] = [
                'employee_id' => $employee->id,
                'type' => 'bonus',
                'amount' => rand(10000, 25000),
                'currency' => 'RUB',
                'title' => 'Премия за качественную работу',
                'description' => 'Дополнительная премия за выполнение проекта в срок',
                'status' => 'pending',
                'due_date' => Carbon::now()->addDays(rand(5, 15)),
                'paid_date' => null,
                'created_at' => Carbon::now()->subDays(rand(1, 5)),
                'updated_at' => Carbon::now()->subDays(rand(1, 5)),
            ];
        }

        // Просроченный платеж (для демонстрации)
        if ($employee->role === 'subcontractor') {
            $finances[] = [
                'employee_id' => $employee->id,
                'type' => 'debt',
                'amount' => rand(5000, 15000),
                'currency' => 'RUB',
                'title' => 'Долг за материалы',
                'description' => 'Возврат средств за закупленные материалы',
                'status' => 'overdue',
                'due_date' => Carbon::now()->subDays(rand(5, 15)),
                'paid_date' => null,
                'created_at' => Carbon::now()->subDays(rand(10, 20)),
                'updated_at' => Carbon::now()->subDays(rand(10, 20)),
            ];
        }

        // Расходы на транспорт
        if (rand(0, 1)) {
            $finances[] = [
                'employee_id' => $employee->id,
                'type' => 'expense',
                'amount' => rand(2000, 8000),
                'currency' => 'RUB',
                'title' => 'Компенсация транспортных расходов',
                'description' => 'Возмещение затрат на проезд к объекту',
                'status' => 'pending',
                'due_date' => Carbon::now()->addDays(rand(1, 7)),
                'paid_date' => null,
                'created_at' => Carbon::now()->subDays(rand(1, 3)),
                'updated_at' => Carbon::now()->subDays(rand(1, 3)),
            ];
        }

        foreach ($finances as $financeData) {
            EmployeeFinance::create($financeData);
        }
    }
}
