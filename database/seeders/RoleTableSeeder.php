<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'Branch Manager',
                'slug' => 'branch-manager',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'Regional Manager',
                'slug' => 'regional-manager',
                'status' => 1,
                'created_at' => time()
            ], 
            [
                'name' => 'Branch User',
                'slug' => 'branch-user',
                'status' => 1,
                'created_at' => time()
            ], 
            [
                'name' => 'Account Manager',
                'slug' => 'account-manager',
                'status' => 1,
                'created_at' => time()
            ], 
            [
                'name' => 'Client Account',
                'slug' => 'client-account',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'Client User',
                'slug' => 'client-user',
                'status' => 1,
                'created_at' => time()
            ],
            
        ];
        foreach ($input as $val) {
            Role::firstOrCreate($val);
        }
    }
}
