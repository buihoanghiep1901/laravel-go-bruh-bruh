<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StageExampleSeeder extends Seeder
{
    public function run(): void
    {
        $time = now();
       DB::table('stage_examples')->insert([
        [
        'name' => 'Nhận hồ sơ',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Pass CV',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Fail CV',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Thông báo kết quả hồ sơ',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Làm bài test',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Pass test',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Fail test',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Thông báo kết quả bài test',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Phỏng vấn',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Fail phỏng vấn',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Offered',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Hired',
        'created_at' => $time,
        'updated_at' => $time
        ], [
        'name' => 'Rejected',
        'created_at' => $time,
        'updated_at' => $time
        ]
        ]);
    }
}