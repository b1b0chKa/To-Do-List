<?php

namespace App\Models;

use App\Models\Article;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\ModelHelpers;
use Illuminate\Console\View\Components\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PhpParser\Node\Expr\FuncCall;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

	protected $hidden =  [
		'password',
		'remember_token'
	];

	protected $casts = [
		'email_verified_at' => 'datetime'
	];

	public function tasks()
	{
		 return $this->hasMany(Task::class);
	}

	public function tags()
	{
		return $this->hasMany(Tag::class);
	}

	public function categories()
	{
		return $this->hasMany(Category::class);
	}
}
