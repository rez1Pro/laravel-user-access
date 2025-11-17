<?php

namespace Rez1pro\UserAccess\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;
use Rez1pro\UserAccess\Enums\BasePermissionEnums;

class PermissionFreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all existing permissions and re-insert them from enums';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting fresh permission sync...');
        $this->newLine();

        // Step 1: Delete all existing permissions
        $this->warn('⚠️  Deleting all existing permissions...');
        $deletedCount = Permission::count();
        
        // Temporarily disable foreign key checks to allow deletion
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->info("✓ Deleted {$deletedCount} permission(s)");
        $this->newLine();

        // Step 2: Insert all permissions from enums
        $this->info('📝 Inserting permissions from enums...');
        $permissions = BasePermissionEnums::getAllPermissionList();

        $insertedCount = 0;
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
            $this->line("  → Created permission: {$permission}");
            $insertedCount++;
        }

        $this->newLine();
        $this->components->success("Successfully refreshed permissions! Inserted {$insertedCount} permission(s). ✅");

        return self::SUCCESS;
    }
}

