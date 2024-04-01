<?php

namespace Database\Seeders;

use App\Models\Department;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        DB::statement("ALTER TABLE `departments` AUTO_INCREMENT = 1");
        DB::table('departments')->delete();
       Department::create(['name' => 'SBU1']);
       Department::create(['name' => 'SBU2']);
       Department::create(['name' => 'SBU3']);
       Department::create(['name' => 'SBU4']);
       Department::create(['name' => 'SBU5']);
    }
}
