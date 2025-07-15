<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Resident\InviteGuestRequest;
use App\Http\Requests\API\Resident\SaveMessageRequest;
use App\Http\Requests\API\Resident\StoreMessageNotificationRequest;
use App\Http\Requests\API\Resident\UpdateAvatarRequest;
use App\Http\Requests\API\Resident\UpdateGuestCodeStatusRequest;
use App\Http\Requests\API\Resident\UpdateGuestValidDateAndTimeRequest;
use App\Http\Requests\API\Resident\UpdateNotificationSettingRequest;
use App\Http\Resources\GuestLocationResource;
use App\Http\Resources\GuestResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\NotificationSettingResource;
use App\Http\Resources\UserLocationResource;
use App\Http\Resources\UserResource;
use App\Jobs\GuestInvitationJob;
use App\Models\Guest;
use App\Models\GuestLocation;
use App\Models\Message;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResidentController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        $user = new UserResource($request->user());
        return ApiResponse::success('Profile fetched successfully', [
            'profile' => $user
        ]);
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $user = $request->user();
        if ($request->hasFile('avatar'))
        {
            if ($user->avatar) {
                $user_avatar = DB::table('users')->where('id', $user->id)->first()->avatar;
                Storage::disk('avatar')->delete($user_avatar);
            }

            // Generate a unique filename
            $filename = uniqid('avatar_') . '.' . $request->file('avatar')->getClientOriginalExtension();

            // Store the uploaded avatar on the 'avatar' disk with the unique filename
            $path = $request->file('avatar')->storeAs('avatars', $filename, 'avatar');

            // Update user's avatar path
            $user->update([
                'avatar' => $path
            ]);

            return ApiResponse::success('Avatar updated', [
                'avatar_link' => $user->avatar
            ]);
        }
        return ApiResponse::failure('Avatar update failed');
    }

    public function listLocations(Request $request)
    {
        $locations = UserLocationResource::collection(UserLocation::paginate());
        return ApiResponse::success('Locations fetched', [
            'locations' => $locations
        ]);
    }

    public function inviteGuest(InviteGuestRequest $request)
    {
        $data = array_merge($request->validated(), [
            'estate_id' => $request->validated('estate_id')
        ]);

        unset($data['estate_id']);
        $guest = Guest::create($data);
        if ($request->validated('email')) {
            $user = $request->user();
            GuestInvitationJob::dispatch($user, $guest);
        }
        return ApiResponse::success('Guest invited successfully', [
            'guest' => new GuestResource($guest)
        ]);
    }

    public function updateGuestCodeStatus(UpdateGuestCodeStatusRequest $request)
    {
        $guest = Guest::where(['user_id' => $request->user()->id, 'invitation_code' => $request->validated()])->first();
        if (!$guest) {
            return ApiResponse::failure('Guest not found', statusCode: Response::HTTP_NOT_FOUND);
        }
        $action = $request->validated('action') === 'activate' ? 'active' : 'inactive';
        $guest->update(['invitation_status' => $action]);

        return ApiResponse::success('Status updated', [
            'guest' => new GuestResource($guest)
        ]);
    }

    public function updateGuestValidDateAndTime(UpdateGuestValidDateAndTimeRequest $request, Guest $guest)
    {
        $guest->update([
            'valid_from_date' => $request->valid_from_date,
            'valid_from_time' => $request->valid_from_time,
            'valid_to_date' => $request->valid_to_date,
            'valid_to_time' => $request->valid_to_time,
        ]);
        return ApiResponse::success('Guess valid date and time updated successfully');
    }

    public function removeGuest(Guest $guest)
    {
        $guest->delete();
        return ApiResponse::success('Guest removed');
    }

    public function listGuests(Request $request)
    {
        $guests = GuestResource::collection(Guest::latest('created_at')->paginate());
        return ApiResponse::success('Guests fetched successfully', [
            'guests' => $guests
        ]);
    }

    public function listGuestsLocationHistory(Request $request)
    {
        $locations = GuestLocation::paginate();
        $data = GuestLocationResource::collection($locations);
        return ApiResponse::success('Locations fetched', [
            'history' => $data
        ]);
    }

    public function listGuestLocationHistory(Request $request, Guest $guest)
    {
        $locations = GuestLocationResource::collection($guest->locations);
        return ApiResponse::success('Location fetched', [
            'location_history' => $locations
        ]);
    }

    public function totalActiveCodesAndGuests()
    {
        $active_codes = Guest::where('invitation_status', 'active')
            ->where('valid_from_date', '<=', date('Y-m-d'))
            ->where('valid_to_date', '>=', date('Y-m-d'))
            ->count();
        $guests = Guest::where('invitation_status', 'active')
            ->where('valid_from_date', '<=', date('Y-m-d'))
            ->where('valid_to_date', '>=', date('Y-m-d'))
            ->whereNotNull('premiss_status')
            ->count();

        return ApiResponse::success('Data fetched successfully', [
            'active_codes' => $active_codes,
            'active_guests' => $guests,
        ]);
    }

    public function saveMessage(SaveMessageRequest $request)
    {
        $sender = $request->user();
        $receiver = $request->input('receiver_type') === 'guest' ? Guest::find($request->input('receiver_id')) : User::find($request->input('receiver_id'));
        $message_body = $request->input('message_body');

        $message = Message::updateOrCreate(
            [
                'sender_id' => $sender->id,
                'sender_type' => $sender->role,
                'receiver_id' => $receiver->id,
                'receiver_type' => $request->input('receiver_type'),
            ],
            [
                'message_body' => $message_body,
                'sender_role' => $sender->role
            ]);

        if (!$message) {
            return ApiResponse::failure('Failed');
        }

        $data = [
            'title' => $sender->full_name,
            'body' => $message_body
        ];

        return ApiResponse::success("success");
    }

    public function fetchMessages(Request $request)
    {
        $user = $request->user();

        $messages = Message::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->paginate();

        return ApiResponse::success('Messages fetched', [
            'messages' => MessageResource::collection($messages)
        ]);
    }

    public function updateNotificationSettings(UpdateNotificationSettingRequest $request)
    {
        $updated = NotificationSetting::updateOrCreate(
            $request->validated()
        );

        if (!$updated) {
            return ApiResponse::failure('Notification Settings update failed');
        }

        $settings = new NotificationSettingResource($updated);

        return ApiResponse::success('Notification Settings updated successfully', [
            'settings' => $settings
        ]);
    }

    public function fetchNotificationSettings()
    {
        $settings = new NotificationSettingResource(NotificationSetting::first());
        return ApiResponse::success('Setting fetched',[
            'settings' => $settings
        ]);
    }

    public function getNotifications()
    {
        $notifications = NotificationResource::collection(Notification::latest()->paginate());

        return ApiResponse::success('Notifications fetched', [
            'notifications' => $notifications
        ]);
    }

    public function readNotification(Notification $notification)
    {
        $notification->update([
            'read_at' => now()
        ]);
        return ApiResponse::success('Notification read');
    }

    public function storeMessageNotification(StoreMessageNotificationRequest $request)
    {
        Notification::create([
        'user_id' => $request->validated('user_id'),
        'type' => 'message',
        'data' => json_encode($request->validated('data'))
        ]);
        return ApiResponse::success('Saved');
    }
}
