<?php

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
