<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'name' => 'John Doe',
                'contact_number' => '0712345678',
                'address' => '123 Main Street',
                'joined_at' => '2023-01-01',
                'salary' => 50000,
                'salary_frequency' => 'Monthly',
                'role' => 'Sales Associate',
                'status' => 'Active',
                'gender' => 'Male',
                'store_id' => 1,
                'balance' => 0,
            ],
            [
                'name' => 'Jane Smith',
                'contact_number' => '0712345679',
                'address' => '456 Elm Street',
                'joined_at' => '2023-02-01',
                'salary' => 55000,
                'salary_frequency' => 'Monthly',
                'role' => 'Cashier',
                'status' => 'Active',
                'gender' => 'Female',
                'store_id' => 1,
                'balance' => 0,
            ],
            [
                'name' => 'Bob Johnson',
                'contact_number' => '0712345680',
                'address' => '789 Oak Street',
                'joined_at' => '2023-03-01',
                'salary' => 45000,
                'salary_frequency' => 'Monthly',
                'role' => 'Stock Clerk',
                'status' => 'Active',
                'gender' => 'Male',
                'store_id' => 1,
                'balance' => 0,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}