<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     description="Category model",
 *     @OA\Property(property="id", type="integer", description="Category ID"),
 *     @OA\Property(property="name", type="string", description="Category name")
 * )
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
