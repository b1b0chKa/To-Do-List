<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\Controller;
use App\Http\Requests\ValidateRequest;
use App\Http\Resources\ResponseJsonFeedback;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function createFeedback(ValidateRequest $request)
	{
		$request->validated();

		$newComment = Feedback::create([
				'name' 			=> $request->name,
				'description' 	=> $request->description
			]);

		return new ResponseJsonFeedback($newComment);
	}
}
