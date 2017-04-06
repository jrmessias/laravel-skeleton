<?php

namespace App\Http\Controllers\Auth;

/**
 * 
 */
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles authenticating users for the application and
      | redirecting them to your home screen. The controller uses a trait
      | to conveniently provide its functionality to your applications.
      |
     */

use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * 
     * @param Provider $provider
     */
    public function redirectToProvider($provider) {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * 
     * @param Provider $provider
     */
    public function handleProviderCallback($provider) {
        $user = Socialite::driver($provider)->user();

        /**
         * Check user share e-mail
         */
        if (is_null($user->getEmail())) {
            flash('É necessário possuir um e-mail público.', 'danger');
            return redirect('login');
        }

        $authUser = User::where('email', $user->getEmail())->first();

        /**
         * Check provider
         */
        /*
          if($authUser->provider !== $provider){
          flash('Você está registrado com outro login.', 'danger');
          return redirect('login');
          }
         */

        /**
         * Find or create user
         */
        $userNewOrCreated = $this->findOrCreateUser($user, $provider);

        /**
         * Login
         */
        Auth::login($userNewOrCreated);

        return redirect($this->redirectTo);
    }

    /**
     * 
     * @param User $user
     * @param Provider $provider
     * @return User
     */
    public function findOrCreateUser($user, $provider) {
        /**
         * Find user
         */
        $authUser = User::where('email', $user->getEmail())->first();

        /**
         * Authenticate with user
         */
        if ($authUser) {
            return $authUser;
        }

        /**
         * Create user
         */
        return User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $user->id,
                    'provider_extra' => (array) $user
        ]);
    }

}
