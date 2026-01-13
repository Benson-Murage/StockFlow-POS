<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Userstamps;

class CashLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Userstamps;

    protected $fillable = [
        'transaction_date',
        'transaction_type',
        'contact_id',
        'reference_id',
        'amount',
        'source',
        'description',
        'store_id',
        'created_by',
    ];

    /**
     * Get the contact (customer/vendor) for this cash log
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /**
     * Get the store for this cash log
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * Get the user who created this cash log
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
