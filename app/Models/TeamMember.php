<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
protected $fillable = [
    'user_id',
    'name',
    'role',
    'email',
    'phone',
    'department',
    'bio',
    'photo',
    'status',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
