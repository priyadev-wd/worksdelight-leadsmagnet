<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Models\GoogleToken;
use App\Models\GoogleCalenderEvent;
use Illuminate\Support\Facades\File;
use DateTime;
use DateTimeZone;

use Illuminate\Support\Facades\Log;

class TriggerAppointmentController extends Controller
{
    public function triggerAppointmentUpdate(Request $request)
    {
        \Log::info('Received Data: ' . json_encode($request->all(), JSON_PRETTY_PRINT));
    }

}
