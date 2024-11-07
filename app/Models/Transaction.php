<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Transaction",
 *     type="object",
 *     description="Transaction model",
 *     @OA\Property(property="id", type="integer", description="Transaction ID"),
 *     @OA\Property(property="wallet_id", type="integer", description="Wallet ID associated with the transaction"),
 *     @OA\Property(property="transaction_type_id", type="integer", description="Transaction type ID"),
 *     @OA\Property(property="category_id", type="integer", description="Category ID associated with the transaction"),
 *     @OA\Property(property="amount", type="number", format="float", description="Transaction amount"),
 *     @OA\Property(property="description", type="string", description="Transaction description"),
 *     @OA\Property(property="date", type="string", format="date-time", description="Transaction date")
 * )
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'transaction_type_id',
        'category_id',
        'amount',
        'description',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

}
