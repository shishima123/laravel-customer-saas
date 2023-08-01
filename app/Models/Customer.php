<?php

namespace App\Models;

use App\Enums\PremiumStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class Customer extends Model
{
    use HasFactory, Billable, Notifiable, SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'company_id',
        'trial_ends_at',
        'next_cycle_date',
        'stripe_id',
        'user_number',
        'billing_contact_email',
        'is_premium',
    ];

    protected $hidden = [
        'subscriptionRelationLast',
    ];

    protected $appends = ['on_grace_period', 'next_cycle_date_format'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_premium' => PremiumStatus::class
    ];

    /**
     * Route notifications for the mail channel.
     *
     * @param \Illuminate\Notifications\Notification $notification
     * @return array|string
     */
    public function routeNotificationForMail($notification): array|string
    {
        return $this->billing_contact_email ?: $this->email;
    }

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function subscriptionRelationLast(): HasOne
    {
        return $this->hasOne(Subscription::class, 'customer_id', 'id')->where('name', 'premium')->latest('id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'customer_id', 'id');
    }

    public function dbConnection(): HasOne
    {
        return $this->hasOne(DbConnection::class, 'customer_id', 'id');
    }

    public function isPremium(): bool
    {
        return $this->subscribed('premium');
    }

    public function isFree(): bool
    {
        return !$this->subscribed('premium');
    }

    public function onGracePeriod(): bool
    {
        return $this->subscription('premium')->onGracePeriod();
    }

    public function hasIncompletePayment(): bool
    {
        return $this->subscription('premium')->hasIncompletePayment();
    }

    public function getTextIncompletePaymentAttribute(): string
    {
        if ($this->hasIncompletePayment()) {
            return __(
                'message.user.subscription_retry_warning',
                [
                    'date' => carbon_parse($this->next_cycle_date)
                        ->addDays(config('services.stripe.date_retry'))
                        ->isoFormat('MMM DD, YYYY')
                ]
            );
        }
        return '';
    }

    public function getNextCycleDateFormatAttribute(): string
    {
        return $this->next_cycle_date ? carbon_parse($this->next_cycle_date)->isoFormat('MMM DD, YYYY') : '';
    }

    public function getOnGracePeriodAttribute(): bool
    {
        $subscriptionRelationLast = $this->subscriptionRelationLast;
        if ($subscriptionRelationLast) {
            return $subscriptionRelationLast->onGracePeriod();
        }
        return false;
    }

    public function isStripeCustomer(): bool
    {
        return !empty($this->stripe_id);
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $user = auth()->user();
        if ($user->isUser() && ($value == 'profile' || $user->id != $value)) {
            return $user->userable;
        }

        return $this->where('id', $value)->firstOrFail();
    }
}
