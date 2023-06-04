<?php

namespace App\Http\Controllers\API;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentStatusController extends Controller
{
    public function getStatusWithCount()
    {
        try {
            $cases = AppointmentStatus::cases();
            $data = collect($cases)->map(function ($status) {
                return [
                    'name' => $status->name,
                    'value' => $status->value,
                    'count' => Appointment::where('status', $status->value)->count(),
                    'color' => AppointmentStatus::from($status->value)->color(),
                ];
            });
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Get data Appointment Status successfully'
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
