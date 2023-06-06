<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function index()
    {
        try {
            $appointments = Appointment::query()
                ->orderBy('id', 'desc')
                ->with('client:id,first_name,last_name')
                ->when(request('status'), function ($query) {
                    return $query->where('status', AppointmentStatus::from(request('status')));
                })
                ->latest()
                ->paginate(env('CUSTOM_PAGING'))
                ->through(fn ($appointment) => [
                    'id' => $appointment->id,
                    'title' => $appointment->title,
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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'client_id' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'description' => 'required',
            ], [
                'client_id.required' => 'The client name field is required.',
            ]);

            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validator->errors()
                ];
                return response()->json($response, 400);
            }

            $appointment = Appointment::create([
                'title' => $request->title,
                'client_id' => $request->client_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'description' => $request->description,
                'status' => AppointmentStatus::SCHEDULED,
            ]);

            $response = [
                'success' => true,
                'data' => $appointment,
                'message' => 'Insert data Appointment successfully'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $appointment = Appointment::find($id);

            $response = [
                'success' => true,
                'data' => $appointment,
                'message' => 'Edit data Appointment successfully'
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
                'title' => 'required',
                'client_id' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'description' => 'required',
            ], [
                'client_id.required' => 'The client name field is required.',
            ]);

            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validator->errors()
                ];
                return response()->json($response, 400);
            }

            $appointment = Appointment::find($id);

            $appointment->update([
                'title' => $request->title,
                'client_id' => $request->client_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'description' => $request->description,
                'status' => AppointmentStatus::SCHEDULED,
            ]);

            $response = [
                'success' => true,
                'data' => $appointment,
                'message' => 'Update data Appointment successfully'
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
            $appointment = Appointment::find($id);

            $appointment->delete();

            $response = [
                'success' => true,
                'message' => 'Delete data Appointments successfully'
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
