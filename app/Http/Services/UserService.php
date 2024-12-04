<?php

namespace App\Http\Services;

use App\Models\Detail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{

    public function findByUsername($username)
    {
        $user = User::where('username', $username)->first();
        if(empty($user)) return [];
        return $user;
    }

    public function save(array $data)
    {
        try {
            $user = $this->findByUsername($data['username']);
            if(empty($user)) {
                $user = User::create([
                    'name' => $data['firstname'],
                    'middle' => $data['middlename'],
                    'last' => $data['lastname'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'type' => $data['type'],
                    'password' => Hash::make('password'),
                ]);
            } else {
                $user->update([
                    'name' => $data['firstname'],
                    'middle' => $data['middlename'],
                    'last' => $data['lastname'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'type' => $data['type'],
                ]);
            }
            
            return $user;
        } catch(\Exception $e) {
            Log::error("Failed Saving User ====> " . $e->getMessage());
            return [];
        }
    }

    public function saveUserDetails(User $user, $details)
    {
        if(!empty($details)) {
            foreach ($details as $key => $value) {
                Detail::updateOrCreate(
                    ['user_id' => $user->id, 'key' => $key],
                    ['value' => $value]
                );
            }
        }
    }

    public function delete($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        return $user->delete();
    }
}
