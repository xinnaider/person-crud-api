<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
