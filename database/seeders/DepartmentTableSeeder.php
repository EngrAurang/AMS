<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $department = [
            [
                'id'    => 1,
                'name' => 'HR Department',
            ],
            [
                'id'    => 2,
                'name' => 'Operations Department',
            ],
            [
                'id'    => 3,
                'name' => 'SEO and Development',
            ],
            [
                'id'    => 4,
                'name' => 'Creative Department',
            ],
            [
                'id'    => 5,
                'name' => 'Marketing Department',
            ],
            [
                'id'    => 6,
                'name' => 'Research Department',
            ],
        ];

        Department::insert($department);
    }
}