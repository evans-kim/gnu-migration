<?php

namespace EvansKim\GnuMigration\Controller;

use App\Http\Controllers\Controller;
use EvansKim\GnuMigration\AuthenticatesGnuMembers;

/**
 * Class LoginController
 * @group Member
 *
 * @package EvansKim\GnuMigration\Controller
 */
class LoginController extends Controller
{
    use AuthenticatesGnuMembers;
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
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}