<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;
    protected $fillable = [
   
        'name',
        'password',
        'phone',
        'status',
        'email',
        'department_id',
        'password_reset_token_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }


    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'collector');
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class);
    }
}