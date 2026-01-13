<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Userstamps;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Userstamps;

    protected $fillable = [
        'name',
        'contact_number',
        'address',
        'joined_at',
        'salary',
        'salary_frequency',
        'role',
        'status',
        'gender',
        'balance',
        'created_by',
        'store_id'
    ];

    protected $casts = [
        'joined_at' => 'date',
    ];

    /**
     * Get all salary records for this employee
     */
    public function salaryRecords()
    {
        return $this->hasMany(SalaryRecord::class, 'employee_id');
    }

    /**
     * Get the latest salary record
     */
    public function latestSalaryRecord()
    {
        return $this->hasOne(SalaryRecord::class, 'employee_id')->latestOfMany('salary_date');
    }

    /**
     * Get total salary paid to this employee
     */
    public function getTotalSalaryPaidAttribute()
    {
        return $this->salaryRecords()->sum('net_salary');
    }

    /**
     * Get last salary payment date
     */
    public function getLastSalaryDateAttribute()
    {
        $lastSalary = $this->salaryRecords()->latest('salary_date')->first();
        return $lastSalary ? $lastSalary->salary_date : null;
    }
}
