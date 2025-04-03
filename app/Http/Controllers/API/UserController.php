<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery\Matcher\HasKey;

use function Laravel\Prompts\password;

class UserController extends Controller
{
    public function register(Request $request)
	{
		$validate = $request->validate([
			'name' 						=> 'required|string|max:100',
			'email' 					=> 'required|email|string|unique:users',
			'password' 					=> 'required|string|min:8|confirmed',
			'password_confirmation' 	=> 'required'
		]);

		$user = User::create([
			'name' => $validate['name'],
			'email' => $validate['email'],
			'password' => Hash::make($validate['password'])
		]);

		$token = $user->createToken('auth_token')->plainTextToken;

		if (!$user)
			return response()->json('Пользователь не создан');

		return response()->json([
			'message' 	=> 'Пользователь создан',
			'user' 		=> $user,
			'token'		=> $token,
		]);
	}

	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required|string|email',
			'password' => 'required'
		]);

		$checkUser = User::where('email', $request->email)->first();
		if (!$checkUser)
		{
			return response()->json([
				'message' => 'нет такого пользователя'
			], 404);
		}

		if (!Auth::attempt($request->only('email', 'password')))
		{
			return response()->json([
				'message' => 'не верные учетные данные'
			], 400);
		}

		$token = Auth::user()->createToken('auth_token')->plainTextToken;

		return response()->json([
			'user' => Auth::user(),
			'token' => $token
		]);
	}

	public function profile(int $user_id)
	{
		$user = User::where('id', $user_id)->with('tasks')->first();

		if (!$user)
		{
			return response()->json([
				'message' => 'такого не существует',
			]);
		}

		return response()->json(['user' => $user]);
	}

	public function updateProfile(Request $request)
	{
		$validate = $request->validate([
			'name' => 'sometimes|string|max:50',
			'email' => 'sometimes|email|string|max:50',
			'password' => 'sometimes|min:8|string'
		]);

		$user = User::where('id', Auth::id())->firstOrFail();

		if (!$user)
			return response()->json(['message' => 'Not auth']);

		$user->update($validate);

		return response()->json([
			'message' => 'Profile is update',
			'new_profile' => $user
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
