<?php

namespace EvansKim\GnuMigration\Controller;


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use EvansKim\GnuMigration\Member;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

/**
 * Class RegisterController
 * @package EvansKim\GnuMigration\Controller
 * @group Member
 */
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * 회원가입
     *
     * @bodyParam mb_id string required 회원 아이디
     * @bodyParam mb_password string required 회원 패스워드
     * @bodyParam mb_name string required 회원명
     * @bodyParam mb_nick string required 회원 닉네임
     * @bodyParam mb_email email required 회원 이메일
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));
        $user->mb_level = 2;
        $user->mb_ip = $request->ip();
        $user->mb_nick_date = Carbon::now();
        $user->save();

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'mb_id' => ['required', 'alpha_dash','unique:g4_member'],
            'mb_name' => ['required', 'string', 'max:255'],
            'mb_nick' => ['required', 'string', 'max:255','unique:g4_member'],
            'mb_email' => ['required', 'string', 'email', 'max:255', 'unique:g4_member'],
            'mb_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Member
     */
    protected function create(array $data)
    {
        return Member::create([
            'mb_id' => $data['mb_id'],
            'mb_name' => $data['mb_name'],
            'mb_nick' => $data['mb_nick'],
            'mb_email' => $data['mb_email'],
            'mb_password' => Member::hash($data['mb_password']),
        ]);
    }
}
