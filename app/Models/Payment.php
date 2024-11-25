<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Cashier;
use App\Enums\PaymentStatus;

class Payment extends Model
{
    use SoftDeletes;

    protected $table = 'payments';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'customer_id', 'type', 'amount', 'currency', 'captured', 'charge_date',
        'stripe_id', 'description', 'failure_code', 'failure_message',
        'invoice_id', 'paid', 'payment_method', 'status', 'amount_refunded',
    ];

    protected $appends = ['charge_date_format', 'amount_format'];

    protected $cast = ['status' => PaymentStatus::class];

    /** Relation with model customer
     *
     * @retun mix
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function getChargeDateFormatAttribute(): string
    {
        return $this->charge_date ? carbon_parse($this->charge_date)->isoFormat('MMM DD, YYYY') : '';
    }

    public function getAmountFormatAttribute(): string
    {
        return Cashier::formatAmount($this->amount, $this->currency);
    }

    public function getIsSucceededStatusAttribute(): bool
    {
        return $this->status == PaymentStatus::SUCCEEDED;
    }

    public function getIsFailedStatusAttribute(): bool
    {
        return $this->status == PaymentStatus::FAILED;
    }
}
