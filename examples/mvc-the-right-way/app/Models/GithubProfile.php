<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GithubProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'preferred_language'
    ];
}
