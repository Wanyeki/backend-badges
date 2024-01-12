<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;
    /**
     * The users that have unlocked this badge
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
