<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\NewAccessToken;

class TokenService
{
    protected const ACCESS_API = 'access-api';
    protected const ISSUE_TOKEN = 'issue-access-token';

    public function createTokens(User $user): array
    {
        return [
            'token' => $this->createAccessToken($user)->plainTextToken,
            'refresh_token' => $this->createRefreshToken($user)->plainTextToken,
        ];
    }

    public function createAccessToken(User $user): NewAccessToken
    {
        return $user->createToken(
            'access_token',
            [self::ACCESS_API],
            Carbon::now()->addMinutes(config('sanctum.ac_expiration'))
        );
    }

    public function createRefreshToken(User $user): NewAccessToken
    {
        return $user->createToken(
            'refresh_token',
            [self::ISSUE_TOKEN],
            Carbon::now()->addMinutes(config('sanctum.rt_expiration'))
        );
    }

    public static function GetAccessApiAbility(): string
    {
        return self::ACCESS_API;
    }

    public static function GetIssueTokenAbility(): string
    {
        return self::ISSUE_TOKEN;
    }
}
