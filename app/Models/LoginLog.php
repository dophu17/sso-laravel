<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'user_name',
        'callback_url',
        'ip_address',
        'user_agent',
        'action',
        'status',
        'session_token',
        'login_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
    ];

    /**
     * Get the user that owns the login log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
