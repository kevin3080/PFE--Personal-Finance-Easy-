<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Wallet",
 *     type="object",
 *     description="Wallet model",
 *     @OA\Property(property="id", type="integer", description="Wallet ID"),
 *     @OA\Property(property="user_id", type="integer", description="User ID associated with the wallet"),
 *     @OA\Property(property="account_type_id", type="integer", description="Account type ID associated with the wallet"),
 *     @OA\Property(property="balance", type="number", format="float", description="Wallet balance")
 * )
 */
class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'account_type_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    public function getBalanceAttribute()
    {
        return $this->accountType->balance;
    }

}
