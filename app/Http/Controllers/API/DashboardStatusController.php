<?php

namespace App\Http\Controllers\API;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class DashboardStatusController extends Controller
{
    public function appointments()
    {
        try {
            $totalAppointmentsCount = Appointment::query()
                ->when(request('status') === 'scheduled', function ($query) {
                    $query->where('status', AppointmentStatus::SCHEDULED);
                })
                ->when(request('status') === 'confirmed', function ($query) {
                    $query->where('status', AppointmentStatus::CONFIRMED);
                })
                ->when(request('status') === 'cancelled', function ($query) {
                    $query->where('status', AppointmentStatus::CANCELLED);
                })
                ->count();

            $response = [
                'success' => true,
                'data' => [
                    'totalAppointmentsCount' => $totalAppointmentsCount,
                ],
                'message' => 'Get data total appointment successfully'
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
