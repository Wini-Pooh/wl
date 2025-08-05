<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    protected $signature = 'users:list';
    protected $description = 'Список всех пользователей в системе';

    public function handle()
    {
        $users = User::with('employeeProfile.partner')->get();
        
        $this->info("=== СПИСОК ПОЛЬЗОВАТЕЛЕЙ ===");
        
        foreach ($users as $user) {
            $isEmployee = $user->isEmployee() ? 'YES' : 'NO';
            $partnerInfo = '';
            
            if ($user->isEmployee()) {
                $partner = $user->getPartner();
                $partnerInfo = $partner ? " (Partner: {$partner->name})" : " (No Partner)";
            }
            
            $this->info("ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Employee: {$isEmployee}{$partnerInfo}");
        }
        
        $this->info("\nВсего пользователей: " . $users->count());
    }
}
