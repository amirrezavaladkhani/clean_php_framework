<?php
namespace App\Modules\User\Controllers;

use App\Modules\User\Models\User;
use Illuminate\Http\Request;

class UserController
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {
        $user = User::create($request->only(['name', 'email', 'password']));
        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->update($request->only(['name', 'email', 'password']));
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function showUsers()
    {
        $users = User::all();
        return view('user.index', ['users' => $users]);
    }
}
