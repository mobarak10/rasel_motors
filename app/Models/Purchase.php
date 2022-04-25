<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $fillable = [
        'date',
        'payment_type',
        'party_id',
        'cash_id',
        'bank_account_id',
        'warehouse_id',
        'voucher_no',
        'subtotal',
        'discount',
        'discount_type',
        'labour_cost',
        'transport_cost',
        'paid',
        'due',
        'previous_balance',
        'note',
        'user_id',
        'business_id',
    ];
    protected $dates = ['date'];
    protected $appends = ['grand_total', 'total_due'];

    /**
     * Purchase details
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseDetails::class);
    }

    /**
     * get grand total
     * @return mixed
     */
    public function getGrandTotalAttribute(){
        return $this->subtotal - $this->discount;
    }

    /**
     * get grand total
     * @return mixed
     */
    public function getTotalDueAttribute(){
        return $this->grand_total - ($this->paid + $this->previous_balance);
    }

    /**
     * Purchase returns
     * @return HasMany
     */
    public function purchaseReturns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    /**
     * Supplier
     * @return BelongsTo
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
