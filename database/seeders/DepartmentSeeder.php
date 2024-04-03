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
       Department::firstOrCreate(['name' => 'SBU1']);
       Department::firstOrCreate(['name' => 'SBU2']);
       Department::firstOrCreate(['name' => 'SBU3']);
       Department::firstOrCreate(['name' => 'SBU4']);
       Department::firstOrCreate(['name' => 'SBU5']);
    }
}
