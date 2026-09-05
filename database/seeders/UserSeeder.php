<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Seed initial Superadmin user mirm09845@gmail.com / 12341234.
     * All existing dummy users will be removed.
     *
     * @return void
     */
    public function run()
    {
        // 1. Wipe all users
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Fetch Superadmin Role
        $superadminRole = Role::where('name', 'superadmin')->first();

        // 3. Create requested Superadmin account (Pre-verified)
        User::create([
            'first_name' => 'Maham',
            'last_name' => 'Mir',
            'name' => 'Maham Mir',
            'email' => 'mirm09845@gmail.com',
            'password' => Hash::make('12341234'),
            'role_id' => $superadminRole ? $superadminRole->id : null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
