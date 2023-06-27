<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;

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

    public function users()
    {
        try {
            $totalUsersCount = User::query()
                ->when(request('date_range') === 'today', function ($query) {
                    $query->whereBetween('created_at', [now()->today(), now()]);
                })
                ->when(request('date_range') === '30_days', function ($query) {
                    $query->whereBetween('created_at', [now()->subDays(30), now()]);
                })
                ->when(request('date_range') === '60_days', function ($query) {
                    $query->whereBetween('created_at', [now()->subDays(60), now()]);
                })
                ->when(request('date_range') === '360_days', function ($query) {
                    $query->whereBetween('created_at', [now()->subDays(360), now()]);
                })
                ->when(request('date_range') === 'month_to_date', function ($query) {
                    $query->whereBetween('created_at', [now()->firstOfMonth(), now()]);
                })
                ->when(request('date_range') === 'year_to_date', function ($query) {
                    $query->whereBetween('created_at', [now()->firstOfYear(), now()]);
                })
                ->count();

            $response = [
                'success' => true,
                'data' => [
                    'totalUsersCount' => $totalUsersCount,
                ],
                'message' => 'Get data total user successfully'
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
