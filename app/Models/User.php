<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;

        protected $dates = ['deleted_at'];


    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'created_by',
        'last_login_at',
    ];

    protected $hidden = ['password'];




    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'created_by');
    }

}
