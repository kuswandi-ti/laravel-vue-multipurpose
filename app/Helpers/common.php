<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

function appointment_status_to_name($status)
{
    switch ($status) {
        case 1:
            $name = 'SCHEDULED';
            break;
        case 2:
            $name = 'CONFIRMED';
            break;
        case 3:
            $name = 'CANCELLED';
            break;
        default:
            $name = '';
            break;
    }
    return $name;
}

function appointment_status_to_color($status)
{
    switch ($status) {
        case 1:
            $color = 'primary';
            break;
        case 2:
            $color = 'success';
            break;
        case 3:
            $color = 'danger';
            break;
        default:
            $color = '';
            break;
    }
    return $color;
}

function setting($key) {
    $settings = Cache::rememberForever('settings', function () {
        return Setting::pluck('value', 'key')->all();
    });

    if (! $settings) {
        $settings = config('settings.default');
    }

    return $settings[$key] ?? false;
}
