<?php

namespace App\Http\Controllers\AuthApp;

use App\Helpers\ApiCodes;
use App\Http\Requests\AuthApp\LoginRequest;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as DomainRequest;
use Laravel\Passport\Client as OClient;

class AuthController extends AuthBaseController
{
    public function login(LoginRequest $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data)) {
            $response = $this->getTokenAndRefreshToken(request('email'), request('password'));

            return $this->successResponseWithData($response);
        }

        return $this->generalError(ApiCodes::getWrongCredentialsMessage(), ApiCodes::UNAUTHENTICATED);
    }

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->header('Refreshtoken');
        $oClient = OClient::where('password_client', 1)->first();
        $http = new Client;

        $domain = DomainRequest::getHttpHost() . '/oauth/token';

        try {

            $payload = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'scope' => '*',
            ];

            $request = Request::create(url('/oauth/token'), 'POST', $payload);
            $response = app()->handle($request);

            $result = json_decode((string) $response->getContent(), true);

            $message = ApiCodes::getSuccessMessage();
            $statusCode = ApiCodes::SUCCESS;

            return $this->responseToJson($message, $statusCode, $result);
        } catch (\Exception $e) {
            return $this->responseToJson(
                'Unauthorized',
                ApiCodes::UNAUTHENTICATED
            );
        }
    }

    public function getTokenAndRefreshToken($email, $password)
    {
        $oClient = OClient::where('password_client', 1)->first();

        $payload = [
            'grant_type' => 'password',
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'username' => $email,
            'password' => $password,
            'scope' => '*',
        ];

        $request = Request::create(url('/oauth/token'), 'POST', $payload);
        $response = app()->handle($request);

        return json_decode((string) $response->getContent(), true);
    }

    public function logout()
    {
        $user = \auth()->user();

        $user->revokeToken();

        return $this->responseToJson(ApiCodes::getSuccessMessage(), ApiCodes::SUCCESS);
    }
}
