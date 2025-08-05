<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class ProjectAccessHelper
{
    /**
     * Проверяет, может ли пользователь получить доступ к проекту
     */
    public static function canAccessProject(User $user, Project $project): bool
    {
        // Админы имеют доступ ко всем проектам
        if ($user->isAdmin()) {
            return true;
        }

        // Партнеры имеют доступ ко всем проектам своих партнеров
        if ($user->isPartner()) {
            return $project->partner_id === $user->id;
        }

        // Для сотрудников проверяем тип роли
        $employee = $user->employeeProfile;
        if (!$employee || $employee->status !== 'active') {
            return false;
        }

        // Проверяем, что проект принадлежит тому же партнеру
        if ($project->partner_id !== $employee->partner_id) {
            return false;
        }

        // Правая рука видит все проекты партнера
        if ($employee->is_right_hand) {
            return true;
        }

        // Прорабы, субподрядчики и сметчики видят только те проекты, где они назначены
        return $project->employees()->where('employee_id', $employee->id)->exists();
    }

    /**
     * Получает список проектов, доступных пользователю
     */
    public static function getAccessibleProjects(User $user): Collection
    {
        if ($user->isAdmin()) {
            return Project::all();
        }

        if ($user->isPartner()) {
            return Project::where('partner_id', $user->id)->get();
        }

        $employee = $user->employeeProfile;
        if (!$employee || $employee->status !== 'active') {
            return collect();
        }

        // Правая рука видит все проекты партнера
        if ($employee->is_right_hand) {
            return Project::where('partner_id', $employee->partner_id)->get();
        }

        // Остальные сотрудники видят только назначенные им проекты
        return $employee->projects;
    }

    /**
     * Получает список сотрудников, которых пользователь может назначить на проект
     */
    public static function getAssignableEmployees(User $user, Project $project = null): Collection
    {
        if ($user->isAdmin()) {
            return Employee::all();
        }

        if ($user->isPartner()) {
            return Employee::where('partner_id', $user->id)
                          ->where('status', 'active')
                          ->get();
        }

        $employee = $user->employeeProfile;
        if (!$employee || $employee->status !== 'active') {
            return collect();
        }

        // Правая рука может назначать всех сотрудников партнера
        if ($employee->is_right_hand) {
            return Employee::where('partner_id', $employee->partner_id)
                          ->where('status', 'active')
                          ->get();
        }

        // Остальные сотрудники могут назначать только тех, кто работает в тех же проектах
        if (!$project) {
            return collect();
        }

        $projectEmployeeIds = $project->employees()->pluck('employee_id')->toArray();
        
        return Employee::whereIn('id', $projectEmployeeIds)
                      ->where('status', 'active')
                      ->get();
    }

    /**
     * Проверяет, является ли сотрудник "правой рукой"
     * @deprecated Используйте поле is_right_hand в модели Employee
     */
    public static function isRightHandEmployee(Employee $employee): bool
    {
        // Новый способ через поле в базе данных
        return $employee->is_right_hand;
    }

    /**
     * Получает список сотрудников, которых можно назначить на проект с учетом прав доступа
     */
    public static function getAssignableEmployeesForProject(User $user, int $partnerId): Collection
    {
        if ($user->isAdmin()) {
            return Employee::where('partner_id', $partnerId)
                          ->where('status', 'active')
                          ->get();
        }

        if ($user->isPartner()) {
            return Employee::where('partner_id', $user->id)
                          ->where('status', 'active')
                          ->get();
        }

        $employee = $user->employeeProfile;
        if (!$employee || $employee->status !== 'active') {
            return collect();
        }

        // Правая рука может назначать всех сотрудников партнера
        if ($employee->is_right_hand) {
            return Employee::where('partner_id', $employee->partner_id)
                          ->where('status', 'active')
                          ->get();
        }

        // Обычные сотрудники не могут назначать других сотрудников
        return collect();
    }

    /**
     * Проверяет, может ли пользователь назначать сотрудников на проекты
     */
    public static function canAssignEmployeesToProject(User $user): bool
    {
        if ($user->isAdmin() || $user->isPartner()) {
            return true;
        }

        $employee = $user->employeeProfile;
        if (!$employee || $employee->status !== 'active') {
            return false;
        }

        // Только правая рука может назначать сотрудников
        return $employee->is_right_hand;
    }

    /**
     * Массово назначает сотрудников на проект
     */
    public static function assignMultipleEmployeesToProject(Project $project, array $employeeIds, array $defaultData = []): array
    {
        $results = [];
        $defaultAssignData = array_merge([
            'status' => 'active',
            'start_date' => now(),
        ], $defaultData);

        foreach ($employeeIds as $employeeId) {
            $employee = Employee::find($employeeId);
            if (!$employee) {
                $results[$employeeId] = ['success' => false, 'message' => 'Сотрудник не найден'];
                continue;
            }

            $assignData = array_merge($defaultAssignData, [
                'role_in_project' => $employee->role,
            ]);

            try {
                $project->employees()->syncWithoutDetaching([$employeeId => $assignData]);
                $results[$employeeId] = ['success' => true, 'message' => 'Успешно назначен'];
            } catch (\Exception $e) {
                Log::error('Error assigning employee to project', [
                    'employee_id' => $employeeId,
                    'project_id' => $project->id,
                    'error' => $e->getMessage()
                ]);
                $results[$employeeId] = ['success' => false, 'message' => 'Ошибка при назначении'];
            }
        }

        return $results;
    }

    /**
     * Удаляет сотрудника из проекта
     */
    public static function removeEmployeeFromProject(Employee $employee, Project $project): bool
    {
        try {
            $project->employees()->detach($employee->id);
            return true;
        } catch (\Exception $e) {
            Log::error('Error removing employee from project', [
                'employee_id' => $employee->id,
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Обновляет статус участия сотрудника в проекте
     */
    public static function updateEmployeeProjectStatus(Employee $employee, Project $project, string $status): bool
    {
        try {
            $project->employees()->updateExistingPivot($employee->id, [
                'status' => $status,
                'updated_at' => now()
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Error updating employee project status', [
                'employee_id' => $employee->id,
                'project_id' => $project->id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Получает роль сотрудника в конкретном проекте
     */
    public static function getEmployeeRoleInProject(Employee $employee, Project $project): ?string
    {
        $pivot = $project->employees()->where('employee_id', $employee->id)->first()?->pivot;
        return $pivot?->role_in_project;
    }
}
