<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TokenService;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class OauthController extends Controller
{
    protected array $availableProviders = [
        'google' => 'google',
        'facebook' => 'facebook',
        // 'instagram' => 'instagrambasic', # Temporarily disabled
        'twitter' => 'twitter-oauth-2',
        'tiktok' => 'tiktok',
    ];

    protected array $statefulProviders = [
        'twitter',
    ];

    protected TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function redirectToProvider($provider)
    {
        if (!$this->isValidProvider($provider)) {
            return response()->json([
                'message' => 'Could not login using this provider'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $driver = Socialite::driver($this->availableProviders[$provider]);

        if (in_array($provider, $this->statefulProviders)) {
            return $driver->redirect();
        }

        return $driver->stateless()->redirect();
    }

    public function handleProviderCallback($provider)
    {
        if (!$this->isValidProvider($provider)) {
            return response()->json([
                'message' => 'Error processing you login request'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $driver = Socialite::driver($this->availableProviders[$provider]);

            if (in_array($provider, $this->statefulProviders)) {
                $user = $driver->user();
                dd(user);
            } else {
                $user = $driver->stateless()->user();
            }
        } catch (ClientException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userCreated = User::firstOrCreate([
            'email' => $user->getEmail()
        ], [
            'email_verified_at' => Carbon::now(),
            'name' => $user->getName(),
            'status' => 1
        ]);

        $userCreated->providers()->updateOrCreate([
            'provider' => $provider,
            'provider_id' => $user->getId()
        ], [
            'avatar' => $user->getAvatar()
        ]);

        $tokens = $this->tokenService->createTokens($userCreated);

        return response()->json($tokens);
    }

    public function handleDeletion($provider)
    {
        //
    }

    protected function isValidProvider($provider): bool
    {
        if (!array_key_exists($provider, $this->availableProviders)) {
            return false;
        }

        return true;
    }
}
