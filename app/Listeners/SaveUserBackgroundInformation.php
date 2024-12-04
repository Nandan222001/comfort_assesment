<?php

namespace App\Listeners;

use App\Events\UserSaved;
use App\Http\Services\UserService;
use App\Models\Detail;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SaveUserBackgroundInformation
{
    protected $userService;
    /**
     * Create the event listener.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserSaved $event)
    {
        $user = $event->user ?? null;
        $payload = $event->payload ?? [];
        
        Log::info('Event User : ' .  json_encode($user?->toArray()));
        Log::info('Event User Detail Payload : ' .  json_encode($payload));

        $middleInitial = strtoupper(substr($payload['middlename'], 0, 1));
        $details = [
            'full_name' => $payload['firstname'] . ' ' . $payload['middlename'] . ' ' . $payload['lastname'],
            'middle_initial' => $middleInitial,
            'gender' => $payload['prefix'] === 'Mr' ? 'Male' : 'Female',
            'suffix' => $payload['suffixname'],
            'avatar' => !empty($payload['photo']) ? $payload['photo'] : null,
        ];

        $detailSaved = $this->userService->saveUserDetails($user, $details);
    }
}
