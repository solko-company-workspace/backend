<?php

namespace App\Http\Controllers;

use App\Constants\AuthMessage;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where('login_id', $request->login_id)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->failure(message: AuthMessage::LOGIN_FAIL, status: 401);
        }

        if (! $user->is_approved) {
            return $this->failure(message: AuthMessage::PENDING_APPROVAL, status: 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $cookie = cookie('token', $token, 60 * 1, '/', null, false, true);

        return $this->success(data: null, message: AuthMessage::LOGIN_SUCCESS)->withCookie($cookie);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'           => 'required|string',
            'member_type_id' => 'required|integer',
            'login_id'       => 'required|string|unique:users',
            'password'       => 'required|string|confirmed',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string|max:500',
            'birth'          => 'nullable|date',
        ]);

        $user = User::create([
            'name'           => $request->name,
            'login_id'       => $request->login_id,
            'password'       => $request->password,
            'member_type_id' => $request->member_type_id,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'birth'          => $request->birth,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(data: ['token' => $token], message: AuthMessage::REGISTER_SUCCESS, status: 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(data: null, message: AuthMessage::LOGOUT_SUCCESS);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? $this->success(message: AuthMessage::PASSWORD_RESET_LINK_SENT)
            : $this->failure(message: AuthMessage::PASSWORD_RESET_LINK_FAIL, status: 400);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only(['email', 'password', 'password_confirmation', 'token']),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->success(message: AuthMessage::PASSWORD_RESET_SUCCESS)
            : $this->failure(message: AuthMessage::PASSWORD_RESET_FAIL, status: 400);
    }
}
