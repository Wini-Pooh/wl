<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class CheckUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-roles {--id= : User ID to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user roles and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('id');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found");
                return;
            }
            $this->checkUser($user);
        } else {
            $this->info('All users and their roles:');
            $users = User::with(['roles', 'defaultRole'])->get();
            
            foreach ($users as $user) {
                $this->checkUser($user);
                $this->line('---');
            }
        }
    }
    
    private function checkUser(User $user): void
    {
        $this->info("User: {$user->name} (ID: {$user->getKey()})");
        $this->line("Email: {$user->email}");
        $this->line("Phone: {$user->phone}");
        
        $this->line("Default Role: " . ($user->defaultRole ? $user->defaultRole->name : 'None'));
        
        $additionalRoles = $user->roles->pluck('name')->toArray();
        $this->line("Additional Roles: " . (count($additionalRoles) ? implode(', ', $additionalRoles) : 'None'));
        
        $this->line("Role checks:");
        $this->line("  - isAdmin(): " . ($user->isAdmin() ? 'true' : 'false'));
        $this->line("  - isPartner(): " . ($user->isPartner() ? 'true' : 'false'));
        $this->line("  - isEmployee(): " . ($user->isEmployee() ? 'true' : 'false'));
        $this->line("  - isForeman(): " . ($user->isForeman() ? 'true' : 'false'));
        $this->line("  - isEstimator(): " . ($user->isEstimator() ? 'true' : 'false'));
        $this->line("  - isClient(): " . ($user->isClient() ? 'true' : 'false'));
        
        $this->line("Button logic (!isClient()): " . (!$user->isClient() ? 'SHOW BUTTONS' : 'HIDE BUTTONS'));
    }
}
