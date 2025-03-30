<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function getAll()
	{
		return response()->json(Auth::user()->tags);
	}

	public function create(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255'
		]);

		$tagCount = Tag::where([
			['name', '=', $request->name],
			['user_id', '=', Auth::id()]
		])->count();

		if ($tagCount>0)
		{
			return response()->json([
				'message' => 'У вас уже есть такой тэг'
			]);
		}

		$tagCreate = Tag::create([
			'name' => $request->name,
			'user_id' => Auth::id(),
		]);

		return response()->json([
			'message' => "Tag is create"
		],201);
	}

	public function update(Request $request, int $tagId)
	{
		$request->validate([
			'name' => 'required|string|max:255'
		]);

		$tagUpdate = Tag::query()
			->where([
				['id', '=', $tagId],
				['user_id', '=', Auth::id()]
			])->first();

		if (!$tagUpdate)
		{
			return response()->json([
				'message' => 'У вас нет такого Тэга'
			]);
		}

		$tagUpdate->update([
			'name' => $request->name
		]);

		return response()->json([
			'message' => 'Тэг обновлен'
		]);
	}

	public function delete(int $tagId)
	{
		$tagDelete = Tag::where([
			['id', '=', $tagId],
			['user_id', '=', Auth::id()]
		])->delete();

		if (!$tagDelete)
		{
			return response()->json([
				'message' => 'Нечего удалять'
			]);
		}

		return response()->json([
			'message' => 'Ваш тэг удален'
		]);
	}
}
