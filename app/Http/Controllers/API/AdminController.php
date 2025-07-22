<?php

namespace App\Http\Controllers\API;

use App\Enums\PremissStatusEnum;
use App\Enums\RoleEnum;
use App\Helpers\ApiResponse;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\CreateAdminRequest;
use App\Http\Requests\API\Admin\CreateEstateLocationRequest;
use App\Http\Requests\API\Admin\CreateEstateRequest;
use App\Http\Requests\API\Admin\CreateResidentLocationRequest;
use App\Http\Requests\API\Admin\CreateResidentRequest;
use App\Http\Requests\API\Admin\GetGuestWithCodeRequest;
use App\Http\Requests\API\Admin\GetGuestWithQrCodeRequest;
use App\Http\Requests\API\Admin\GuestCheckInOrOutRequest;
use App\Http\Requests\API\Admin\ListGuestsFilterRequest;
use App\Http\Requests\API\Admin\UpdateGuestEntryPermissionRequest;
use App\Http\Requests\API\Admin\UpdateResidentStatusRequest;
use App\Http\Resources\EstateLocationResource;
use App\Http\Resources\EstateResource;
use App\Http\Resources\GuestLocationResource;
use App\Http\Resources\GuestResource;
use App\Http\Resources\UserResource;
use App\Jobs\WelcomeAndVerificationMailJob;
use App\Mail\WelcomeAndVerificationMail;
use App\Models\Estate;
use App\Models\EstateLocation;
use App\Models\Guest;
use App\Models\GuestLocation;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function createEstateLocation(CreateEstateLocationRequest $request)
    {
        $location = EstateLocation::create($request->validated());
        return ApiResponse::success('Estate location created successfully', [
            'estate_location' => new EstateLocationResource($location)
        ]);
    }

    public function listEstateLocation()
    {
        $locations = EstateLocation::all();
        return ApiResponse::success('Estate locations fetched successfully', [
            'estate_locations' => EstateLocationResource::collection($locations)
        ]);
    }

    public function listResidents()
    {
        $residents = User::where('role', RoleEnum::RESIDENT->value)->get();
        return ApiResponse::success('Residents fetched successfully', [
            'residents' => UserResource::collection($residents)
        ]);
    }

    public function fetchResidentByQR(User $resident)
    {
        return ApiResponse::success('Resident fetched', [
            'resident' => new UserResource($resident)
        ]);
    }

    public function createResident(CreateResidentRequest $request)
    {
        $data = array_merge($request->validated(), [
            'role' => RoleEnum::RESIDENT->value,
        ]);
        $resident = User::create($data);
        $resident->update([
            'qr_code' => CommonHelper::generateQrCode($resident->id)
        ]);

        WelcomeAndVerificationMailJob::dispatchSync($resident, $request->validated('password'));

        return ApiResponse::success("Sign in details have been sent to {$resident->email}");
    }

    public function removeResident(User $resident)
    {
        if ($resident->role !== RoleEnum::RESIDENT->value) {
            return ApiResponse::failure('This User is not a resident');
        }
        $resident->delete();

        return ApiResponse::success('Resident Removed successfully');
    }

    public function updateResidentStatus(UpdateResidentStatusRequest $request)
    {
        $user = User::find($request->validated('user_id'));
        $status = $request->validated('action');
        if ($status === 'activate')
        {
            $user->is_active = true;
        }

        if ($status === 'deactivate')
        {
            $user->is_active = false;
        }
        $user->save();

        return ApiResponse::success("Resident status {$status}d successfully");
    }

    public function assignResidentLocation(CreateResidentLocationRequest $request)
    {
        $location = UserLocation::create($request->validated());
        return ApiResponse::success('Resident Location Created', [
            'location' => $location
        ]);
    }

    public function getGuestDetailsWithCode(GetGuestWithCodeRequest $request)
    {
        $guest = Guest::where('invitation_code', $request->validated('code'))->first();
        if (!$guest) {
            return ApiResponse::failure('Guest not found', statusCode: 404);
        }

        return ApiResponse::success('Guest fetched', [
            'guest' => new GuestResource($guest)
        ]);
    }

    public function getGuestByQRCode(GetGuestWithQrCodeRequest $request)
    {
         $guest = Guest::find($request->validated('uuid'));

         return ApiResponse::success('Guest fetched', [
            'guest' => new GuestResource($guest)
         ]);
    }

    public function guestCheckInOrOut(GuestCheckInOrOutRequest $request)
    {
        $guest = Guest::find($request->validated('guest_id'));
        $action = $request->validated('action');

        if ($action === 'checkin')
        {
            $guest->premiss_status = PremissStatusEnum::IN->value;
        }

        if ($action === 'checkout')
        {
            $guest->premiss_status = PremissStatusEnum::EXITED->value;
        }
        $guest->admin_note = $request->validated('note');
        $guest->premiss_status_updated_at = Carbon::now();
        $guest->save();

        return ApiResponse::success('Guest premiss status updated successfully', [
            'guest' => new GuestResource($guest)
        ]);
    }

    public function updateGuestEntryPermission(UpdateGuestEntryPermissionRequest $request)
    {
        $guest = Guest::find($request->validated('guest_id'));

        if ($request->validated('entry_permission') == 'allow') {
            $guest->entry_permission_status = 'allowed';
        }

        if ($request->validated('entry_permission') == 'reject') {
            $guest->entry_permission_status = 'rejected';
        }

        $guest->entry_permission_status_updated_at = now();

        if ($request->validated('entry_permission_reason')) {
            $guest->entry_permission_reason = $request->entry_permission_reason;
        }

        $guest->save();

        return ApiResponse::success('Guest attended to successfully');
    }

    public function getGuestLocationHistory(Guest $guest)
    {
        $location_history = $guest->locations()->latest();
        return ApiResponse::success('Location fetched', [
            'location_history' => $location_history
        ]);
    }

    public function getTotalActiveCodesAndGuests()
    {
        $active_codes = Guest::where('valid_from_date', '<=', Carbon::now()->toDate())
            ->where('valid_to_date', '>=', Carbon::now()->toDate())
            ->count();
        $guests = Guest::where('valid_from_date', '<=', Carbon::now()->toDate())
            ->where('valid_to_date', '>=', Carbon::now()->toDate())
            ->whereNotNull('premiss_status')
            ->count();

        return ApiResponse::success('Data fetched successfully', [
            'active_codes' => $active_codes,
            'active_guests' => $guests,
            'all_guests' => Guest::count()
        ]);
    }

    public function listGuests(ListGuestsFilterRequest $request)
    {
        $guests = Guest::guestFilter(['filter' => $request->validated('filter'), 'invitation_status' => $request->validated('invitation_status')])
            ->latest('created_at')
            ->get();

        return ApiResponse::success('Guests fetched', [
            'guests' => GuestResource::collection($guests)
        ]);
    }

    public function getGuestsByLocation(Estate $estate)
    {
        $guests = $estate->guests;
        return ApiResponse::success('Guests history fetched', [
            'history' => $guests
        ]);
    }

    public function recentActivities()
    {
        $activities = GuestLocation::latest()->paginate(30);

        return ApiResponse::success('Activities Fetched', [
            'activities' => GuestLocationResource::collection($activities)
        ]);
    }
}
