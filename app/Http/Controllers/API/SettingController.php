<?php

namespace App\Http\Controllers\API;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    public function update(Request $request)
    {
        try {
            $settings = $request->all();
            foreach ($settings as $key => $value) {
                Setting::where('key', $key)->update([
                    'value' => $value
                ]);
            }

            $response = [
                'success' => true,
                'data' => $settings,
                'message' => 'Update data Setting successfully'
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
