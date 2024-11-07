<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="AccountType",
 *     type="object",
 *     description="Account Type model",
 *     @OA\Property(property="id", type="integer", description="Account Type ID"),
 *     @OA\Property(property="type_account", type="string", description="Account Type (e.g., Savings, Checking)"),
 *     @OA\Property(property="balance", type="number", format="float", description="Account balance")
 * )
 */
class AccountType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_account',
        'balance',
    ];

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }
}
