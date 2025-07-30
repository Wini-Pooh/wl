<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeFinance;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class EmployeeFinanceController extends Controller
{
    public function __construct()
    {
        // Доступ к финансам сотрудников только для партнеров, сотрудников и админов (НЕ прорабов)
        $this->middleware(['auth', 'role:partner,employee,admin']);
    }

    /**
     * Добавляет финансовую запись
     */
    public function store(Request $request, Employee $employee)
    {
        Log::info('Employee finance store method called', [
            'employee_id' => $employee->id,
            'user_id' => Auth::id(),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'expects_json' => $request->expectsJson(),
            'headers' => $request->headers->all()
        ]);

        // Проверка доступа
        if ($employee->partner_id !== Auth::id()) {
            Log::warning('Attempt to access employee finances without permission', [
                'employee_id' => $employee->id,
                'employee_partner_id' => $employee->partner_id,
                'user_id' => Auth::id(),
                'user_roles' => Auth::user()->roles->pluck('name')->toArray()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Нет доступа к финансам этого сотрудника'], 403);
        }

        Log::info('Creating employee finance record', [
            'employee_id' => $employee->id,
            'request_data' => $request->all(),
            'user_id' => Auth::id()
        ]);

        try {
            $validated = $request->validate([
                'type' => ['required', Rule::in(array_keys(EmployeeFinance::getTypes()))],
                'amount' => 'required|numeric|min:0.01',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'due_date' => 'required|date',
                'project_id' => 'nullable|exists:projects,id',
            ]);

            $validated['employee_id'] = $employee->id;
            $validated['status'] = 'pending';
            $validated['currency'] = 'RUB'; // По умолчанию рубли

            $finance = EmployeeFinance::create($validated);

            Log::info('Employee finance record created successfully', [
                'finance_id' => $finance->id,
                'employee_id' => $employee->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Финансовая запись добавлена',
                'finance' => $finance->load('employee', 'project')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error in employee finance creation', [
                'employee_id' => $employee->id,
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации данных: ' . implode(', ', $e->validator->errors()->all()),
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Ошибка создания финансовой записи: ' . $e->getMessage(), [
                'employee_id' => $employee->id,
                'request_data' => $request->all(),
                'exception_class' => get_class($e),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при создании записи: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Показывает конкретную финансовую запись
     */
    public function show(Employee $employee, EmployeeFinance $finance)
    {
        // Проверка доступа
        if ($employee->partner_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Нет доступа'], 403);
        }

        if ($finance->employee_id !== $employee->id) {
            return response()->json(['success' => false, 'message' => 'Запись не найдена'], 404);
        }

        return response()->json($finance->load('employee', 'project'));
    }

    /**
     * Обновляет финансовую запись
     */
    public function update(Request $request, Employee $employee, EmployeeFinance $finance)
    {
        Log::info('Employee finance update method called', [
            'employee_id' => $employee->id,
            'finance_id' => $finance->id,
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        // Проверка доступа
        if ($employee->partner_id !== Auth::id()) {
            Log::warning('Attempt to update employee finance without permission', [
                'employee_id' => $employee->id,
                'finance_id' => $finance->id,
                'employee_partner_id' => $employee->partner_id,
                'user_id' => Auth::id()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Нет доступа к финансам этого сотрудника'], 403);
        }

        if ($finance->employee_id !== $employee->id) {
            return response()->json(['success' => false, 'message' => 'Запись не найдена'], 404);
        }

        try {
            $validated = $request->validate([
                'type' => ['required', Rule::in(array_keys(EmployeeFinance::getTypes()))],
                'amount' => 'required|numeric|min:0.01',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'due_date' => 'required|date',
                'project_id' => 'nullable|exists:projects,id',
            ]);

            $finance->update($validated);

            Log::info('Employee finance record updated successfully', [
                'finance_id' => $finance->id,
                'employee_id' => $employee->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Финансовая запись обновлена',
                'finance' => $finance->load('employee', 'project')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error in employee finance update', [
                'employee_id' => $employee->id,
                'finance_id' => $finance->id,
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации данных: ' . implode(', ', $e->validator->errors()->all()),
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Ошибка обновления финансовой записи: ' . $e->getMessage(), [
                'employee_id' => $employee->id,
                'finance_id' => $finance->id,
                'request_data' => $request->all(),
                'exception_class' => get_class($e),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при обновлении записи: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удаляет финансовую запись
     */
    public function destroy(Employee $employee, EmployeeFinance $finance)
    {
        // Проверка доступа
        if ($employee->partner_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Нет доступа'], 403);
        }

        if ($finance->employee_id !== $employee->id) {
            return response()->json(['success' => false, 'message' => 'Запись не найдена'], 404);
        }

        try {
            $finance->delete();
            return response()->json(['success' => true, 'message' => 'Финансовая запись удалена']);
        } catch (\Exception $e) {
            Log::error('Ошибка удаления финансовой записи: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ошибка при удалении записи'], 500);
        }
    }

    /**
     * Отмечает финансовую запись как оплаченную
     */
    public function markAsPaid(Request $request, Employee $employee, EmployeeFinance $finance)
    {
        // Проверка доступа
        if ($employee->partner_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Нет доступа'], 403);
        }

        if ($finance->employee_id !== $employee->id) {
            return response()->json(['success' => false, 'message' => 'Запись не найдена'], 404);
        }

        try {
            $paidDate = $request->input('paid_date', now()->toDateString());
            $finance->markAsPaid($paidDate);
            return response()->json(['success' => true, 'message' => 'Платеж отмечен как выполненный']);
        } catch (\Exception $e) {
            Log::error('Ошибка обновления статуса платежа: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ошибка при обновлении статуса'], 500);
        }
    }

    /**
     * Проверяет и обновляет просроченные платежи
     */
    public function checkOverdue(Employee $employee)
    {
        // Проверка доступа
        if ($employee->partner_id !== Auth::id()) {
            abort(403);
        }

        $overdueFinances = $employee->finances()
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->get();

        $updated = 0;
        foreach ($overdueFinances as $finance) {
            $finance->markAsOverdue();
            $updated++;
        }

        return response()->json([
            'success' => true,
            'updated' => $updated,
            'message' => $updated > 0 ? "Обновлено {$updated} просроченных платежей" : 'Просроченных платежей не найдено'
        ]);
    }
}
