<?php

namespace EvansKim\GnuMigration;

use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Str;

class Member extends Authenticatable
{
    use Notifiable;

    protected $table="g4_member";
    protected $primaryKey = 'mb_no';
    public $incrementing = false;
    const UPDATED_AT = null;
    const CREATED_AT = "mb_datetime";


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'mb_password', 'mb_remember',
    ];

    protected $fillable = ['mb_id', 'mb_password', 'mb_email', 'mb_name', 'mb_nick', 'mb_birth','mb_sex'];

    static public function hash($value)
    {
        return \DB::select("select password(?) as password", [$value])[0]->password;
    }
    public function getEmailForPasswordReset(){
        return $this->mb_email;
    }
    public function routeNotificationFor($driver, $notification = null)
    {
        if (method_exists($this, $method = 'routeNotificationFor'.Str::studly($driver))) {
            return $this->{$method}($notification);
        }

        switch ($driver) {
            case 'database':
                return $this->notifications();
            case 'mail':
                return $this->mb_email;
            case 'nexmo':
                return $this->mb_hp;
        }
    }
    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'mb_id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->mb_id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->mb_password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->mb_remember;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->mb_remember = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'mb_remember';
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}