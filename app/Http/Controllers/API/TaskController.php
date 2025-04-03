<?php

namespace App\Http\Controllers\API;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function getAll()
    {
		return response()->json(Task::where([
				['user_id', '=', Auth::id()]
			])->get());
    }

    public function create(Request $request)
    {
		$validated = $request->validate([
			'title' 		=> 'required|string|max:50',
			'description' 	=> 'nullable|string|max:255',
			'status' 		=> 'required|in:pending,in_progress,completed',
			'category_id' 	=> 'nullable|exists:categories,id',
			'tags' 			=> 'nullable|array',
			'tags.*' 		=> 'exists:tags,id'
		]);
	
		$task = Task::create([
			'title' 		=> $validated['title'],
			'description' 	=> $validated['description'] ?? null,
			'status' 		=> $validated['status'],
			'user_id' 		=> Auth::id(),
			'category_id' 	=> $validated['category_id'] ?? null
		]);
	
		if (!empty($validated['tags'])) {
			$task->tags()->sync($validated['tags']);
		}
	
		return response()->json(['message' => 'Задача создана'], 201);
    }

    public function getById(int $taskId)
    {
		$user = Auth::user();

		$taskExists = Task::where([
			['id', '=', $taskId],
			['user_id','=', $user->id]
		])->with('tags')->first();


		if(!$taskExists)
			return response()->json(['message' => 'У вас нет такой задачи']);
		
		return response()->jwson($taskExists->toArray(), 200);
    }

    public function update(Request $request, int $taskId)
    {
		$validated = $request->validate([
            'title' => 'sometimes|string|max:50',
            'description' => 'nullable|string|max:255',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        $task = Task::where('id', '=', $taskId)
            ->where('user_id', '=', Auth::id())
            ->firstOrFail();

        $task->update($validated);

        if (array_key_exists('tags', $validated))
            $task->tags()->sync($validated['tags']);


        return response()->json(['message' => 'Задача обновлена']);
    }

    public function delete(int $taskId)
    {
		$isDeleted = Task::query()
			->where([
				['id', '=', $taskId],
				['user_id', '=', Auth::user()->id]
			])->delete();

		if (!$isDeleted)
		{
			return response()->json([
				'message' => 'Not Delete'
			]);
		}

		return response()->json([
			'message' => 'Delete success'
		]);
    }
}