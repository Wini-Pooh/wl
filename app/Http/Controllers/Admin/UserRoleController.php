<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
    
    /**
     * Показывает список всех пользователей с их ролями
     */
    public function index()
    {
        $users = User::with(['roles', 'defaultRole'])->get();
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }
    
    /**
     * Показывает форму для редактирования ролей пользователя
     */
    public function edit($id)
    {
        $user = User::with(['roles', 'defaultRole'])->findOrFail($id);
        $roles = Role::all();
        
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    /**
     * Обновляет роли пользователя
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Обновляем роль по умолчанию
        $user->default_role_id = $request->default_role_id;
        $user->save();
        
        // Обновляем связанные роли
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $user->roles()->detach();
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Роли пользователя обновлены успешно.');
    }
}
