<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\User;
use App\Models\ApiKey;

class MatchesUserApiKey implements Rule
{
    public function passes($attribute, $value)
    {
        $apikey = ApiKey::where('key', request()->header('apikey'))->first();

        if (!$apikey) {
            return false;
        }

        return $apikey->user_name == request()->header('username');
    }

    public function message()
    {
        return 'The provided API key does not match the provided username.';
    }
}
