<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;

class AppointmentController extends Controller
{
    public function index()
    {
        try {
            $appointments = Appointment::query()
                ->with('client:id,first_name,last_name')
                ->when(request('status'), function ($query) {
                    return $query->where('status', AppointmentStatus::from(request('status')));
                })
                ->latest()
                ->paginate(env('CUSTOM_PAGING'))
                ->through(fn ($appointment) => [
                    'id' => $appointment->id,
                    'start_time' => Carbon::parse($appointment->start_time)->format('Y-m-d h:i A'),
                    'end_time' => Carbon::parse($appointment->end_time)->format('Y-m-d h:i A'),
                    'status' => [
                        'name' => appointment_status_to_name($appointment->status),
                        'color' => appointment_status_to_color($appointment->status),
                    ],
                    'client' => $appointment->client,
                ]);

            $response = [
                'success' => true,
                'data' => $appointments,
                'message' => 'Get data Appointments successfully'
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
