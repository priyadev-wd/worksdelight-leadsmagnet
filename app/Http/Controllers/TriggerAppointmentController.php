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
    public function triggerConfirmAppointment(Request $request)
    {
        //\Log::info('Received Data: ' . json_encode($request->all(), JSON_PRETTY_PRINT));
        $current_date_time = date("Y-m-d H:i:s");
        $appointmentStatus = "Confirm";
        $contact_id = $request->contact_id;
        //$contact_id = "3COmhPLHbLuEmmdATPGb";

        \Log::info('Contact ID : ' . $contact_id);
        \Log::info('Appointment Status : ' . $appointmentStatus);
        if ($contact_id != "") {

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('AUTH_BEARER_TOKEN'),
                'Version' => '2021-07-28',
            ])->get('https://services.leadconnectorhq.com/contacts/' . $contact_id . '/appointments');

            if ($response->successful()) {
                $data = json_decode($response, true);
                $events = $data['events'];

                $closest_event = $this->findClosestEvent($events, $current_date_time);

                if ($closest_event) {
                    \Log::info('Closest Event : ' . json_encode($closest_event, JSON_PRETTY_PRINT));

                    $this->updateAppointment($closest_event['id'], $closest_event['contactId'],$closest_event['title'], $appointmentStatus);

                } else {
                    \Log::error('No Event Found');
                }

            } else {
                \Log::info('Error in Appointment Update : ' . json_encode($response->body(), JSON_PRETTY_PRINT));
                dd("Error in appointment Update " . $response->body());
            }
        }else{
            \Log::info('Contact Id Not Present');
        }
    }

    public function triggerCancelAppointment(Request $request)
    {
        \Log::info('Received Data: ' . json_encode($request->all(), JSON_PRETTY_PRINT));
        $current_date_time = date("Y-m-d H:i:s");
        $appointmentStatus = "Cancel";
        $contact_id = $request->contact_id;
        // $contact_id = "3COmhPLHbLuEmmdATPGb";

        \Log::info('Contact ID : ' . $contact_id);
        \Log::info('Appointment Status : ' . $appointmentStatus);
        if ($contact_id != "") {

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('AUTH_BEARER_TOKEN'),
                'Version' => '2021-07-28',
            ])->get('https://services.leadconnectorhq.com/contacts/' . $contact_id . '/appointments');

            if ($response->successful()) {
                $data = json_decode($response, true);
                $events = $data['events'];

                $closest_event = $this->findClosestEvent($events, $current_date_time);

                if ($closest_event) {
                    \Log::info('Closest Event : ' . json_encode($closest_event, JSON_PRETTY_PRINT));

                    $this->updateAppointment($closest_event['id'], $closest_event['contactId'], $closest_event['title'], $appointmentStatus);

                } else {
                    \Log::error('No Event Found');
                }

            } else {
                \Log::info('Error in Appointment Update : ' . json_encode($response->body(), JSON_PRETTY_PRINT));
                dd("Error in appointment Update " . $response->body());
            }
        }else{
            \Log::info('Contact Id Not Present');
        }
    }

    public function updateAppointment($eventId,$contactId,$event_title,$appointmentStatus)
    {
        if($appointmentStatus == "Confirm")
        {
            $title = "✔️ ".$event_title;
        }

        if($appointmentStatus == "Cancel")
        {
            $title = "❌ ".$event_title;
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.env('AUTH_BEARER_TOKEN'),
            'Content-Type' => 'application/json',
            'Version' => '2021-04-15',
        ])->put('https://services.leadconnectorhq.com/calendars/events/appointments/'.$eventId, [
            'calendarId' => env('CALENDAR_ID'),
            'locationId' => env('LOCATION_ID'),
            'contactId' => $contactId,
            "title" => $title,
            "appointmentStatus" => "confirmed",
        ]);

        if ($response->successful()) {
            \Log::info('Appointment Updated : ' . json_encode($response, JSON_PRETTY_PRINT));
            dd("Appointment Updated ".$response->body());
        } else {
            \Log::info('Error in Appointment Update : ' . json_encode($response->body(), JSON_PRETTY_PRINT));
            dd("Error in appointment Update ".$response->body());
        }
    }

    function findClosestEvent($events, $current_date_time) {
        $closest_event = null;
        $min_diff = PHP_INT_MAX;

        foreach ($events as $event) {
            $event_start_time = $event['startTime'];
            $diff = abs(strtotime($event_start_time) - strtotime($current_date_time));

            if ($diff < $min_diff) {
                $min_diff = $diff;
                $closest_event = $event;
            }
        }

        return $closest_event;
    }

}
