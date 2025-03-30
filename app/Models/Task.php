<?php

namespace App\Models;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

	protected $fillable = [
		'title',
		'description',
		'category_id',
		'user_id',
		'status'
	];

	public function user()
	{
		return $this->belongsTo( User::class);
	}

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function tags()
	{
		return $this->belongsToMany(Tag::class, 'task_tag');
	}
}
