<?php
namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getAll()
	{
		return response()->json(Category::all());
	}

	public function create(Request $request)
	{

		$request->validate([
			'name' => 'required|string|max:50'
		]);
		$categoryCount = Category::where([
			['user_id', '=', auth()->user()->id],
			['name', '=', $request->name]
		])->count();

		if ($categoryCount > 0)
		{
			return response()->json([
				'message' => 'У вас уже есть такая категория'
			]);
		}

		$categoryCreate = Category::create([
			'name' => $request->name,
			'user_id' => auth()->user()->id
		]);

		return response()->json([
			'message' 	=> 'Category is created'
		], 201);
	}

	public function update(Request $request, int $categoryId)
	{
		$categoryUpdate =  Category::query()
			->where([
					['id', '=', $categoryId],
					['user_id', '=', auth()->user()->id]
				]
			)
			->update([
				'name'		=> $request->name
		]);

		$request->validate([
			'name' 		=> 'required|string|max:255'
		]);

		if(!$categoryUpdate)
			return response()->json([
				'message' => 'not update'
		], 403);

		return response()->json([
			'message' 	=> 'Category is update'
		]);
	}

	public function delete(int $categoryId)
	{
		//check auth
		$categoryDelete = Category::query()
			->where([
				['id', '=', $categoryId],
				['user_id', '=', auth()->user()->id]
			])
			->delete();

		if (!$categoryDelete)
		{
			return response()->json([
				'message' => 'Category is not delete'		
			], 403);
		}

		return response()->json([
			'message' 	=> 'Category is deleted'
		]);
	}
}
