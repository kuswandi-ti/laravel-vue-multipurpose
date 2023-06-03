<?php

namespace App\Http\Controllers\API;

use App\Enums\RoleType;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::latest()->get();

            $response = [
                'success' => true,
                'data' => $users,
                'message' => 'Get data User successfully'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|sometimes|min:8',
            ]);

            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validator->errors()
                ];
                return response()->json($response, 400);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $response = [
                'success' => true,
                'data' => $user,
                'message' => 'Insert data User successfully'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
            ]);

            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validator->errors()
                ];
                return response()->json($response, 400);
            }

            $user = User::find($id);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $response = [
                'success' => true,
                'data' => $user,
                'message' => 'Update data User successfully'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);

        $user->delete();

        $response = [
            'success' => true,
            'message' => 'Delete data User successfully'
        ];

        return response()->json($response, 200);
    }
}
