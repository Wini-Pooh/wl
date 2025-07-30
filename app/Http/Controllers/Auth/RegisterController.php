<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/', 'unique:users'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Находим роль client
        /** @var \App\Models\Role|null $clientRole */
        $clientRole = Role::where('name', 'client')->first();
        
        // Создаем пользователя с ролью клиента по умолчанию
        /** @var \App\Models\User $user */
        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'default_role_id' => $clientRole?->id,
        ]);
        
        // Также добавляем роль через связь many-to-many
        if ($clientRole) {
            $user->assignRole($clientRole);
        }
        
        // Проверяем, есть ли сотрудник с этим номером телефона без привязки к пользователю
        $this->checkAndLinkEmployee($user);
        
        return $user;
    }
    
    /**
     * Проверяет и привязывает сотрудника к новому пользователю
     */
    private function checkAndLinkEmployee(User $user)
    {
        try {
            // Ищем сотрудника с таким же номером телефона без привязки к пользователю
            $employee = Employee::where('phone', $user->phone)
                                ->whereNull('user_id')
                                ->first();
            
            if ($employee) {
                // Привязываем пользователя к сотруднику
                $employee->update(['user_id' => $user->id]);
                
                // Назначаем пользователю роль сотрудника
                $this->assignEmployeeRole($user, $employee->role);
                
                Log::info('Employee automatically linked to new user', [
                    'user_id' => $user->id,
                    'employee_id' => $employee->id,
                    'partner_id' => $employee->partner_id,
                    'role' => $employee->role
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error linking employee to new user: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'phone' => $user->phone
            ]);
        }
    }
    
    /**
     * Назначает роль сотрудника в зависимости от его роли в Employee
     */
    private function assignEmployeeRole(User $user, string $employeeRole)
    {
        $roleMapping = [
            'foreman' => 'foreman',
            'subcontractor' => 'employee', // субподрядчик получает роль сотрудника
            'estimator' => 'estimator',
        ];

        $systemRole = $roleMapping[$employeeRole] ?? 'employee';
        /** @var \App\Models\Role|null $role */
        $role = Role::where('name', $systemRole)->first();
        
        if ($role) {
            // Удаляем все роли кроме клиента, затем добавляем новую
            /** @var \App\Models\Role|null $clientRole */
            $clientRole = Role::where('name', 'client')->first();
            if ($clientRole) {
                $user->roles()->sync([$clientRole->id, $role->id]);
                
                // Устанавливаем роль сотрудника как основную
                $user->default_role_id = $role->id;
                $user->save();
            }
        }
    }
}
