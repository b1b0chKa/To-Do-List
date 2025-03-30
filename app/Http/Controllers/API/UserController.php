<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
	{
		$request->validate([
			'name' 		=> 'required|string|max:255',
			'email' 	=> 'required|string|email|unique:users',
			'password' 	=> 'required|string|min:8|confirmed',
			'password_confirmation' => 'required'
		]);

		$user = User::create([
			'name' 		=> $request->name,
			'email' 	=> $request->email,
			'password'	=> Hash::make($request->password),
		]);

		$token = $user->createToken('auth_token')->plainTextToken;

		return response()->json([
			'user' 	=> $user,
			'token'	=> $token
		], 201);
	}

	public function login(Request $request)
	{
		$request->validate([
			'email' 	=> 'required|email|string',
			'password' 	=> 'required'
		]);

		$user = User::where('email', $request->email)->first();

		if (!$user)
			return response()->json(['message' => 'User not found'], 404);

		if (!Auth::attempt($request->only('email', 'password')))
			return response()->json(['message' => 'Неверные учетные данные'], 401);

		$user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;
		
		return response()->json([
			'user'	=> $user,
			'token'	=> $token
		]);
	}

	public function profile(int $user_id)
	{
		if (!DB::table('users')->find($user_id))
		{
			return response()->json([
				'message'=>'Такого пользователя нет'
			]);
		}

		return response()->json(User::find($user_id));
	}

	public function updateProfile(Request $request)
	{
		$user = Auth::user();
		// dd($user);
		$request->validate([
			'name' 	=> 'sometimes|string|min:2',
			'email' => 'sometimes|email|string|max:255|unique:users,email',
			'password' => 'sometimes|min:8|confirmed'
		]);

		if ($request->has('name'))
			$user->name = $request->name;
	
		if ($request->has('email'))
			$user->email = $request->email;


		if ($request->has('password'))
			$user->password = Hash::make($request->password);

		$user->save();

		return response()->json([
			'message' 	=> 'Profile update',
			'user' 		=> $user,
		]);
	}

	public function logout()
	{
		Auth::user()->tokens()->delete();
		
		return response()->json(['message' => 'You are log out']);
	}

	public function deleteProfile()
	{
		$user = Auth::user();

		$user->delete();

		return response()->json(['message' => 'You are delete account']);
	}
}
