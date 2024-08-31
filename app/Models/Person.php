<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Person",
 *     type="object",
 *     title="Person",
 *     required={"name", "title", "birth_date", "relationship"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="title", type="string", example="Mr."),
 *     @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="relationship", type="string", enum={"single", "married", "divorced", "widowed"}, example="single"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 */

class Person extends Model
{
    protected $table = 'persons';
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
