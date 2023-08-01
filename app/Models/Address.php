<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasUuid, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'add1',
        'add2',
        'state',
        'zipcode',
        'city_id',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'city_id', 'id');
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'address_id', 'id');
    }
}
