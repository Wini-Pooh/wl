<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeFinance;
use App\Models\Project;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function __construct()
    {
        // Доступ к управлению сотрудниками только для партнеров, сотрудников и админов (НЕ прорабов)
        // Сотрудники имеют полный доступ как правая рука партнера
        $this->middleware(['auth', 'role:partner,employee,admin']);
    }

    /**
     * Получает ID партнера для текущего пользователя
     */
    private function getPartnerId()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return null; // Админ видит всех
        }
        
        if ($user->isPartner()) {
            return $user->id;
        }
        
        if ($user->isForeman() && $user->employeeProfile) {
            return $user->employeeProfile->partner_id;
        }
        
        return null;
    }

    /**
     * Отображает список сотрудников
     */
    public function index(Request $request)
    {
        $query = Employee::with(['finances' => function($q) {
            $q->where('status', 'pending')->orderBy('due_date');
        }]);

        // Фильтрация по партнеру (только свои сотрудники, кроме админа)
        if (!Auth::user()->isAdmin()) {
            // Для прорабов используем ID партнера из профиля, для партнеров - собственный ID
            $partnerId = Auth::user()->isPartner() ? Auth::id() : Auth::user()->employeeProfile?->partner_id;
            if ($partnerId) {
                $query->forPartner($partnerId);
            } else {
                // Если не удается определить партнера, показываем пустой результат
                $query->whereRaw('1 = 0');
            }
        }

        // Фильтры
        if ($request->filled('role')) {
            $query->byRole($request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(15);

        // Статистика
        $stats = [
            'total' => Employee::forPartner(Auth::id())->count(),
            'active' => Employee::forPartner(Auth::id())->active()->count(),
            'pending_payments' => EmployeeFinance::whereHas('employee', function($q) {
                if (!Auth::user()->hasRole('admin')) {
                    $q->forPartner(Auth::id());
                }
            })->pending()->sum('amount'),
            'overdue_payments' => EmployeeFinance::whereHas('employee', function($q) {
                if (!Auth::user()->hasRole('admin')) {
                    $q->forPartner(Auth::id());
                }
            })->overdue()->sum('amount'),
        ];

        return view('partner.employees.index', compact('employees', 'stats'));
    }

    /**
     * Показывает форму создания нового сотрудника
     */
    public function create()
    {
        $roles = Employee::getRoles();
        $statuses = Employee::getStatuses();
        
        return view('partner.employees.create', compact('roles', 'statuses'));
    }

    /**
     * Сохраняет нового сотрудника
     */
    public function store(Request $request)
    {
        // Сценарий 1: Пользователь выбрал существующий аккаунт через user_id
        $existingUser = null;
        if ($request->filled('user_id')) {
            $existingUser = User::find($request->user_id);
            
            // Проверяем, что этот пользователь еще не является сотрудником данного партнера
            $existingEmployee = Employee::where('user_id', $existingUser->id)
                                      ->where('partner_id', Auth::id())
                                      ->first();
            
            if ($existingEmployee) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Этот пользователь уже добавлен как сотрудник!');
            }
        } else {
            // Сценарий 2: Проверяем существование пользователя по телефону
            $existingUser = User::where('phone', $request->phone)->first();
        }
        
        // Валидация с учетом сценария
        $phoneValidationRules = 'required|string|max:20';
        
        // Проверяем уникальность телефона среди сотрудников данного партнера
        $phoneValidationRules .= '|unique:employees,phone,NULL,id,partner_id,' . Auth::id();
        
        // Если пользователь не выбран через user_id и не существует в системе, 
        // проверяем уникальность в таблице users
        if (!$request->filled('user_id') && !$existingUser) {
            $phoneValidationRules .= '|unique:users,phone';
        }
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'phone' => $phoneValidationRules,
            'email' => 'nullable|email|max:255',
            'role' => ['required', Rule::in(array_keys(Employee::getRoles()))],
            'status' => ['required', Rule::in(array_keys(Employee::getStatuses()))],
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'user_id' => 'nullable|exists:users,id',
            
            // Поля для начального финансового обязательства
            'finance_type' => ['nullable', Rule::in(array_keys(EmployeeFinance::getTypes()))],
            'finance_amount' => 'nullable|numeric|min:0',
            'finance_title' => 'nullable|string|max:255',
            'finance_due_date' => 'nullable|date|after_or_equal:today',
        ]);

        $validated['partner_id'] = Auth::id();
        
        // Обработка двух сценариев
        if ($request->filled('user_id')) {
            // Сценарий 1: Привязка к существующему аккаунту
            $validated['user_id'] = $existingUser->id;
            
            // Если у пользователя другой телефон, обновляем его данные
            if ($existingUser->phone !== $validated['phone']) {
                $existingUser->update(['phone' => $validated['phone']]);
            }
            
            // Обновляем имя пользователя если оно не заполнено
            if (empty($existingUser->name) || $existingUser->name === $existingUser->phone) {
                $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name']);
                $existingUser->update(['name' => $fullName]);
            }
            
        } else if ($existingUser) {
            // Сценарий 1a: Найден пользователь по телефону, но не был выбран явно
            $validated['user_id'] = $existingUser->id;
        }
        // Сценарий 2: Пользователь не зарегистрирован - создаем только запись сотрудника без user_id
        
        $employee = Employee::create($validated);

        // Обработка пользователя для сотрудника
        $userResult = $this->handleUserForEmployee($employee, $existingUser, $request->filled('user_id'));

        // Создаем начальное финансовое обязательство, если данные указаны
        if ($request->filled(['finance_type', 'finance_amount', 'finance_title', 'finance_due_date'])) {
            EmployeeFinance::create([
                'employee_id' => $employee->id,
                'type' => $validated['finance_type'],
                'amount' => $validated['finance_amount'],
                'currency' => 'RUB',
                'title' => $validated['finance_title'],
                'due_date' => $validated['finance_due_date'],
                'status' => 'pending',
            ]);
        }

        $successMessage = 'Сотрудник успешно добавлен!';
        if (isset($userResult['message'])) {
            $successMessage .= ' ' . $userResult['message'];
        }

        return redirect()->route('partner.employees.show', $employee)
                        ->with('success', $successMessage);
    }

    /**
     * Отображает детали сотрудника
     */
    public function show(Employee $employee)
    {
        // Проверка доступа
        if (!Auth::user()->hasRole('admin') && $employee->partner_id !== Auth::id()) {
            abort(403);
        }

        $employee->load(['finances' => function($q) {
            $q->orderBy('due_date', 'desc');
        }, 'projects']);

        // Статистика по финансам
        $financeStats = [
            'total_pending' => $employee->getTotalPendingAmount(),
            'total_paid' => $employee->getTotalPaidAmount(),
            'overdue' => $employee->getOverdueAmount(),
            'upcoming' => $employee->finances()->dueSoon(7)->sum('amount'),
        ];

        // Группируем платежи по датам
        $upcomingPayments = $employee->getPendingPaymentsByDate();

        return view('partner.employees.show', compact('employee', 'financeStats', 'upcomingPayments'));
    }

    /**
     * Показывает форму редактирования сотрудника
     */
    public function edit(Employee $employee)
    {
        // Проверка доступа
        if (!Auth::user()->hasRole('admin') && $employee->partner_id !== Auth::id()) {
            abort(403);
        }

        $roles = Employee::getRoles();
        $statuses = Employee::getStatuses();
        
        return view('partner.employees.edit', compact('employee', 'roles', 'statuses'));
    }

    /**
     * Обновляет данные сотрудника
     */
    public function update(Request $request, Employee $employee)
    {
        // Проверка доступа
        if (!Auth::user()->hasRole('admin') && $employee->partner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'phone' => ['required', 'string', 'max:20', Rule::unique('employees', 'phone')->ignore($employee->id), Rule::unique('users', 'phone')->ignore($employee->phone, 'phone')],
            'email' => 'nullable|email|max:255',
            'role' => ['required', Rule::in(array_keys(Employee::getRoles()))],
            'status' => ['required', Rule::in(array_keys(Employee::getStatuses()))],
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldRole = $employee->role;
        $employee->update($validated);

        // Если роль изменилась, обновляем роль пользователя
        if ($oldRole !== $validated['role']) {
            $this->updateUserRole($employee, $validated['role']);
        }

        return redirect()->route('partner.employees.show', $employee)
                        ->with('success', 'Данные сотрудника обновлены!');
    }

    /**
     * Удаляет сотрудника
     */
    public function destroy(Employee $employee)
    {
        // Проверка доступа
        if (!Auth::user()->hasRole('admin') && $employee->partner_id !== Auth::id()) {
            abort(403);
        }

        // Меняем роль пользователя на клиент
        $this->changeUserToClient($employee);

        $employee->delete();

        return redirect()->route('partner.employees.index')
                        ->with('success', 'Сотрудник удален!');
    }

    /**
     * Поиск сотрудника по номеру телефона
     */
    public function searchByPhone(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string'
            ]);

            $phone = $request->input('phone');
            $query = Employee::where('phone', 'like', '%' . $phone . '%');

            // Фильтрация по партнеру (только свои сотрудники, кроме админа)
            $user = Auth::user();
            if ($user && !$user->hasRole('admin')) {
                $query->forPartner(Auth::id());
            }

            $employees = $query->with('finances')->get()->map(function($employee) {
                return [
                    'id' => $employee->id,
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'middle_name' => $employee->middle_name,
                    'phone' => $employee->phone,
                    'email' => $employee->email,
                    'role' => $employee->role,
                    'role_name' => $employee->role_name,
                    'status' => $employee->status,
                    'status_name' => $employee->status_name,
                    'full_name' => $employee->full_name,
                ];
            });

            return response()->json($employees);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка поиска сотрудника по телефону: ' . $e->getMessage());
            return response()->json(['error' => 'Ошибка при поиске сотрудников'], 500);
        }
    }

    /**
     * Показывает дашборд финансов сотрудников
     */
    public function dashboard()
    {
        $query = EmployeeFinance::whereHas('employee', function($q) {
            if (!Auth::user()->hasRole('admin')) {
                $q->forPartner(Auth::id());
            }
        });

        // Основная статистика
        $stats = [
            'total_pending' => (clone $query)->pending()->sum('amount'),
            'total_paid' => (clone $query)->paid()->sum('amount'),
            'overdue' => (clone $query)->overdue()->sum('amount'),
            'upcoming_week' => (clone $query)->dueSoon(7)->sum('amount'),
        ];

        // Распределение по типам
        $byType = (clone $query)->pending()
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->get()
            ->pluck('total', 'type');

        // Предстоящие платежи (10 ближайших)
        $upcomingPayments = (clone $query)->pending()
            ->with(['employee', 'project'])
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        // Сотрудники с наибольшими задолженностями
        $employeeQuery = Employee::with(['finances' => function($q) {
            $q->where('status', 'pending')->orWhere('status', 'overdue');
        }]);

        if (!Auth::user()->hasRole('admin')) {
            $employeeQuery->forPartner(Auth::id());
        }

        $topDebtors = $employeeQuery->get()->map(function($employee) {
            $employee->total_pending = $employee->finances->where('status', 'pending')->sum('amount');
            $employee->overdue_amount = $employee->finances->where('status', 'overdue')->sum('amount');
            $employee->last_payment_date = $employee->finances()->where('status', 'paid')->latest('paid_date')->first()?->paid_date;
            return $employee;
        })->filter(function($employee) {
            return $employee->total_pending > 0 || $employee->overdue_amount > 0;
        })->sortByDesc('total_pending')->take(10);

        return view('partner.employees.dashboard', compact(
            'stats', 'byType', 'upcomingPayments', 'topDebtors'
        ));
    }

    /**
     * Обрабатывает пользователя для сотрудника (создание или привязка существующего)
     */
    private function handleUserForEmployee(Employee $employee, $existingUser = null, $wasUserSelected = false)
    {
        if ($existingUser) {
            // Сценарий 1: Пользователь уже зарегистрирован
            
            // Если user_id еще не установлен, устанавливаем его
            if (!$employee->user_id) {
                $employee->update(['user_id' => $existingUser->id]);
            }
            
            // Назначаем роль сотрудника соответствующую роли в Employee
            $this->assignEmployeeRole($existingUser, $employee->role);
            
            if ($wasUserSelected) {
                return [
                    'status' => 'linked',
                    'message' => 'Существующий аккаунт пользователя привязан к сотруднику.'
                ];
            } else {
                return [
                    'status' => 'auto_linked',
                    'message' => 'Найден существующий аккаунт с этим номером телефона и автоматически привязан.'
                ];
            }
        } else {
            // Сценарий 2: Пользователь не зарегистрирован
            // Создаем запись сотрудника без привязки к пользователю
            // Пользователь сможет зарегистрироваться позже и автоматически получит партнера
            
            return [
                'status' => 'pending',
                'message' => 'Сотрудник добавлен. При регистрации в системе пользователь автоматически получит доступ.'
            ];
        }
    }

    /**
     * Создает пользователя для сотрудника и назначает роль (старый метод для совместимости)
     */
    private function createUserForEmployee(Employee $employee)
    {
        return $this->handleUserForEmployee($employee);
    }

    /**
     * Ищет пользователей по номеру телефона
     */
    public function searchUsersByPhone(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string'
            ]);

            $phone = $request->input('phone');
            
            // Поиск пользователей по телефону
            $users = User::where('phone', 'like', '%' . $phone . '%')
                ->with('roles')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'roles' => $user->roles->pluck('name')->implode(', '),
                        'is_registered' => true
                    ];
                });

            // Поиск существующих сотрудников по телефону
            $employees = Employee::where('phone', 'like', '%' . $phone . '%')
                ->where('partner_id', Auth::id())
                ->get()
                ->map(function($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->full_name,
                        'phone' => $employee->phone,
                        'email' => $employee->email,
                        'role' => $employee->role_name,
                        'is_employee' => true
                    ];
                });

            return response()->json([
                'users' => $users,
                'employees' => $employees
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка поиска пользователей по телефону: ' . $e->getMessage());
            return response()->json(['error' => 'Ошибка при поиске пользователей'], 500);
        }
    }

    /**
     * Назначает роль сотруднику в зависимости от его роли в Employee
     */
    private function assignEmployeeRole(User $user, string $employeeRole)
    {
        $roleMapping = [
            'foreman' => 'foreman',
            'subcontractor' => 'employee', // субподрядчик получает роль сотрудника
            'estimator' => 'estimator',
        ];

        $systemRole = $roleMapping[$employeeRole] ?? 'employee';
        $role = Role::where('name', $systemRole)->first();
        
        if ($role) {
            // Удаляем все роли кроме клиента, затем добавляем новую
            $clientRole = Role::where('name', 'client')->first();
            $user->roles()->sync([$clientRole->id, $role->id]);
            
            // Устанавливаем роль сотрудника как основную
            $user->default_role_id = $role->id;
            $user->save();
        }
    }

    /**
     * Обновляет роль пользователя при изменении роли сотрудника
     */
    private function updateUserRole(Employee $employee, string $newRole)
    {
        $user = User::where('phone', $employee->phone)->first();
        
        if ($user) {
            $this->assignEmployeeRole($user, $newRole);
        }
    }

    /**
     * Меняет пользователя на клиента при удалении сотрудника
     */
    private function changeUserToClient(Employee $employee)
    {
        $user = User::where('phone', $employee->phone)->first();
        
        if ($user) {
            $clientRole = Role::where('name', 'client')->first();
            
            if ($clientRole) {
                // Удаляем все роли кроме клиента
                $user->roles()->sync([$clientRole->id]);
                
                // Устанавливаем роль клиента как основную
                $user->default_role_id = $clientRole->id;
                $user->save();
            }
        }
    }
}
