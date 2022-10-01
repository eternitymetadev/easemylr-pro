<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //sidebar option name
        $input = [
            [
                'name' => 'users',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'locations',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'consigners',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'consignees',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'drivers',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'vehicles',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'consignments',
                'status' => 1,
                'created_at' => time()
            ],    
            [
                'name' => 'payments',
                'status' => 1,
                'created_at' => time()
            ], 
            

        ];
        foreach ($input as $val) {
            Permission::firstOrCreate($val);
        }
    }
}
