<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Userstamps;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Userstamps;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'balance',
        'loyalty_points',
        'type',   // Type of contact: customer or vendor
        'whatsapp'
    ];


    // $customers = Contact::customers()->get();
    public function scopeCustomers($query)
    {
        return $query->where('type', 'customer');
    }

    // Contact::vendors()->get();
    public function scopeVendors($query)
    {
        return $query->where('type', 'vendor');
    }

    public function incrementBalance($amount, $user)
    {
        // Step 1: Get the current balance
        $previousBalance = $this->balance;

        // Step 2: Increment the balance
        $this->increment('balance', $amount);

        // Step 3: Log the activity with previous balance, new balance, and user context
        activity()
            ->performedOn($this)
            ->causedBy($user)
            ->withProperties([
                'previous_balance' => $previousBalance,
                'incremented_amount' => $amount,
                'new_balance' => $this->balance,
            ])
            ->log('Increased balance from ' . $previousBalance . ' to ' . $this->balance);
    }

    public function quotations() {
        return $this->hasMany(Quotation::class);
    }

    /**
     * Get all sales for this contact (customer)
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, 'contact_id');
    }

    /**
     * Get all purchases for this contact (vendor)
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'contact_id');
    }

    /**
     * Get all transactions for this contact
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'contact_id');
    }

    /**
     * Get total sales amount for this customer
     */
    public function getTotalSalesAttribute()
    {
        return $this->sales()->sum('total_amount');
    }

    /**
     * Get total purchases amount for this vendor
     */
    public function getTotalPurchasesAttribute()
    {
        return $this->purchases()->sum('total_amount');
    }
}
