<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class UserController extends Controller
{
    // use AuthenticatesUsers, ThrottlesLogins;
    protected $maxAttempts = 3;
    protected $decayMinutes = 0.5;

    public function showResetPasswordForm()
    {
        return view('reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users'],
        ]);
    }

    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        // $user = Models\User::create([
        //     'name' => request('name'),
        //     'email' => request('email'),
        //     'password' => Hash::make('password')
        // ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:10',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{10,}$/'
            ]
        ]);

        // auth()->login($user);

        if ($validator->fails()) {
            return redirect('register')
                ->withErrors($validator)
                ->withInput();
        } else {
            return redirect('login');
        }
    }

    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // $throttles = $this->hasTooManyLoginAttempts($request);

        // if ($throttles) {
        //     $this->fireLockoutEvent($request);

        //     return $this->sendLockoutResponse($request);
        // }

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'captcha' => 'required|captcha'
            // 'g-recaptcha-response' => ['required', new GoogleRecaptcha]
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'captcha' => 'required|captcha'
        ]);

        if ($validator->fails()) {
            $this->incrementLoginAttempts($request);
            return redirect('login')
                ->withErrors($validator)
                ->withInput();
        } else return redirect('welcome');
    }
}
