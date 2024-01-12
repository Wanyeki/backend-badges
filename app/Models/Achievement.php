<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;
      /**
     * The users that have unlocked this achievement
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
