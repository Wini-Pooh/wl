<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description'];
    
    /**
     * Пользователи, которые принадлежат к этой роли.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    
    /**
     * Пользователи, которые имеют эту роль по умолчанию.
     */
    public function defaultUsers()
    {
        return $this->hasMany(User::class, 'default_role_id');
    }
}
