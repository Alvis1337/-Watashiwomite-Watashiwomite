<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MalUser extends Model
{
    use HasFactory;

    /**
     * @throws Exception
     */

    protected $fillable = [
        'user_id',
        'user_name',
        'picture_url',
        'access_token',
        'refresh_token',
        'expires_in',
        'has_expired',
    ];

    protected $table = 'mal_users';
    public $timestamps = false;
}
