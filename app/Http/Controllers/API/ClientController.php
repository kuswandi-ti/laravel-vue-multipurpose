<?php

namespace App\Http\Controllers\API;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    public function index()
    {
        try {
            $clients = Client::latest()->orderBy('first_name', 'asc')->get();

            $response = [
                'success' => true,
                'data' => $clients,
                'message' => 'Get data Client successfully'
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
