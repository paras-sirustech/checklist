<?php

function json_response($data = [], $status = 200, array $headers = [], $options = 0)
{
    return response()->json($data, $status, $headers, $options);
}

function formattedDateTimeString($time, $format='M d, Y \a\t h:i A')
{
    if ($time!='') {
        if ($time instanceof Carbon\Carbon) {
            return $time->format($format);
        } else {
            $formatted = new DateTime($time);
            return $formatted->format($format);
        }
    }
}