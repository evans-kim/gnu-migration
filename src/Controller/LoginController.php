<?php

namespace EvansKim\GnuMigration\Controller;

use App\Http\Controllers\Controller;
use EvansKim\GnuMigration\AuthenticatesGnuMembers;

/**
 * Class LoginController
 * @group 그누보드 사용자 인증
 *
 * 사용자 로그인, 회원가입, 비밀번호 변경, 찾기
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