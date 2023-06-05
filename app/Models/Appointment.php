<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'client_id',
    //     'title',
    //     'start_time',
    //     'end_time',
    //     'description',
    //     'status'
    // ];

    protected $guarded = [];

    protected $cast = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => AppointmentStatus::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
