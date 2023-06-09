<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::query()
                ->when(request('query'), function ($query, $searchQuery) {
                    $query->where('name', 'like', "%{$searchQuery}%");
                })
                ->orderBy('id', 'desc')
                ->latest()
                ->paginate(env('CUSTOM_PAGING'));

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
                'role' => RoleType::USER->value,
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
        try {
            $user = User::find($id);

            $user->delete();

            $response = [
                'success' => true,
                'message' => 'Delete data User successfully'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function changeRole(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role' => 'required',
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
                'role' => $request->role,
            ]);

            $response = [
                'success' => true,
                'data' => $user,
                'message' => 'Update data Role User successfully'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $user = User::whereIn('id', $request->ids);

            $user->delete();

            $response = [
                'success' => true,
                'message' => 'Bulk Delete data User successfully'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|sometimes|min:8',
                'c_password' => 'required|same:password',
            ], [
                'c_password.required' => 'Confirmation password is a required field',
                'c_password.same' => 'Password and Confirmation Password are not the same',
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
                'role' => RoleType::USER->value,
            ]);
            $user['token'] = $user->createToken('auth_token')->plainTextToken;

            // After register, try to login
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $response = [
                    'success' => true,
                    'data' => $user,
                    'message' => 'Register User successfully'
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validator->errors()
                ];
                return response()->json($response, 400);
            }

            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = User::where('email',  $request->email)->first();
                $user->tokens()->delete();
                $response = [
                    'success' => true,
                    'data' => [
                        'email' => $user->email,
                        'token' => $user->createToken('auth_token')->plainTextToken,
                    ],
                    'message' => 'User login successfully'
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'These credentials do not match our records !!!'
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            Session::flush();
            $response = [
                'success' => true,
                'message' => 'User logout successfully'
            ];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
