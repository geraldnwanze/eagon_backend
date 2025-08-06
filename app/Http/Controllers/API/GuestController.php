<?php

namespace App\Http\Controllers\API;

use App\Enums\PremissStatusEnum;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Guest\AcceptInviteRequest;
use App\Http\Requests\API\Guest\ExitEstateRequest;
use App\Http\Requests\API\Guest\FetchMessageRequest;
use App\Http\Requests\API\Guest\SaveCurrentLocationRequest;
use App\Http\Requests\API\Guest\SaveMessageRequest;
use App\Http\Resources\GuestLocationResource;
use App\Http\Resources\GuestResource;
use App\Http\Resources\MessageResource;
use App\Models\Guest;
use App\Models\GuestLocation;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GuestController extends Controller
{
    public function acceptInvite(AcceptInviteRequest $request)
    {
        $guest = Guest::where('invitation_code', $request->validated('invitation_code'))->orWhere('email', $request->validated('email'))->first();
        if (!$guest) {
            return ApiResponse::failure('No guest found');
        }
        $start = Carbon::parse($guest->valid_from_date . ' ' . $guest->valid_from_time, 'UTC');
        $end = Carbon::parse($guest->valid_to_date . ' ' . $guest->valid_to_time, 'UTC');
        $current = Carbon::now('UTC')->addHour();

        $is_valid = $current->between($start, $end, true);

        if ($guest->status !== 'inactive' && $is_valid) {
            $guest->update([
                'guest_accepted' => true,
                'fcm_token' => $request->input('fcm_token')
            ]);

            return ApiResponse::success('Invitation Accepted', [
                'guest' => new GuestResource($guest)
            ]);
        }

        if (!$is_valid) {
            return ApiResponse::failure('Code not valid for this time period');
        }

        return ApiResponse::failure('Contact the resident that invited you');
    }

    public function exitEstate(ExitEstateRequest $request)
    {
        $guest = Guest::where('invitation_code', $request->validated('invitation_code'))->first();
        $guest->update([
            'premiss_status' => 'exited',
            'premiss_status_updated_at' => now()
        ]);
        return ApiResponse::success('Guest Exited Successfully', [
            'guest' => $guest
        ]);
    }

    private function checkProximity($assignedLocationLat, $assignedLocationLong, $currentLocationLat, $currentLocationLong)
    {
        $destination = $assignedLocationLat . ',' . $assignedLocationLong;
        $origin = $currentLocationLat . ',' . $currentLocationLong;

        $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
            'origin' => $origin,
            'destination' => $destination,
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);

        $data = $response->json();

        if ($data['status'] === 'REQUEST_DENIED') {
            return false;
        }

        $distance = $data['routes'][0]['legs'][0]['distance']['value'];

        if ($distance > 200) {
            return true;
        }

        return false;
    }

    public function saveCurrentLocation(SaveCurrentLocationRequest $request)
    {
        $guest = Guest::where('invitation_code', $request->validated('invitation_code'))->first();
        $outsideProximity = $this->checkProximity($guest->estate->latitude, $guest->estate->longitude, $request->input('latitude'), $request->input('longitude'));

        if (!$outsideProximity) {
            return ApiResponse::failure('Something went wrong, please try again later');
        }

        if ($guest && $guest->guest_accepted) {
            $location = GuestLocation::create([
                'guest_id' => $guest->id,
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
                'location_name' => $request->input('location_name')
            ]);

            if ($guest->premiss_status === PremissStatusEnum::IN->value) {
                $updatedAt = Carbon::parse($guest->premiss_status_updated_at);
                $currentTime = Carbon::today();
                $is_more_than_30_minutes = $currentTime->diffInMinutes($updatedAt) > 30;

                if ($is_more_than_30_minutes) {
                    if ($outsideProximity) {


                        return ApiResponse::success('Guest is outside proximity', [
                            'location' => new GuestLocationResource($location)
                        ]);
                    }
                }
            }

            return ApiResponse::success('Location saved', [
                'location' => new GuestLocationResource($location)
            ]);
        }

        return ApiResponse::failure('Guest could not be found');
    }

    public function saveMessage(SaveMessageRequest $request)
    {
        $guest = Guest::where('invitation_code', $request->validated('invitation_code'))->first();
        $message_body = $request->validated('message_body');
        $message = Message::updateOrCreate([
            'sender_id' => $guest->id,
            'sender_type' => 'guest',
            'receiver_id' => $guest->user->id,
            'receiver_type' => 'resident',
        ], [
            'message_body' => $message_body,
            'sender_role' => 'guest'
        ]);

        if (!$message) {
            return ApiResponse::failure('Message Failed');
        }

        if ($guest->user->fcm_token) {

            $data = [
                'title' => $guest->full_name,
                'body' => $message_body
            ];
            return ApiResponse::success("success");
        }
        return ApiResponse::success("success");
    }

    public function fetchMessages(FetchMessageRequest $request)
    {
        $guest = Guest::where($request->validated('invitation_code'))->first();
        $messages = Message::where('sender_id', $guest->id)->orWhere('receiver_id', $guest->id)->paginate();

        return ApiResponse::success('Messages fetched', [
            'messages' => MessageResource::collection($messages)
        ]);
    }
}
