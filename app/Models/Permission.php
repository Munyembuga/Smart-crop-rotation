<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category'
    ];

    /**
     * Get the roles that have this permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Get the users that have this permission directly
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions');
    }
}
