<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLeaf extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'employee_leaves';

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const HR_APPROVAL_RADIO = [
        'Pending'    => 'Pending',
        'Approved'   => 'Approved',
        'Unapproved' => 'Unapproved',
    ];

    public const LINE_MANAGER_APPROVAL_RADIO = [
        'Pending'    => 'Pending',
        'Approved'   => 'Approved',
        'Unapproved' => 'Unapproved',
    ];

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'line_manager_approval',
        'hr_approval',
        'leave_type',
        'leave_reason',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
}