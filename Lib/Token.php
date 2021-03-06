<?php

namespace Tayami\Sosso\Lib;

use Tayami\Sosso\Core\Sso;

class Token
{
    const TOKEN_REQUEST_KEY = 'so_sso_token_id';

    public static function checkToken($token)
    {
        $authInfo = null;
        $token = Encryption::decrypt($token);
        if($realAuth = Sso::driver()->get(Auth::HASH_KEY_TOKEN_TO_AUTH_PRE.$token)) {
            if(Sso::driver()->sIsMember(Auth::SET_KEY_AUTH_TOKENS_PRE.$realAuth, $token)) {
                $userInfo = Auth::realAuthToInfo($realAuth);
            } else {
                Sso::driver()->del(Auth::HASH_KEY_TOKEN_TO_AUTH_PRE.$token);
            }
        }
        return $authInfo;
    }

    public static function createToken()
    {
        $realAuth = Auth::localGetRealAuthKey();
        if(Auth::realAuthToInfo($realAuth)) {
            $token = Encryption::randStr();
            Sso::driver()->setex(Auth::HASH_KEY_TOKEN_TO_AUTH_PRE.$token, Sso::expire(), $realAuth);
            Sso::driver()->sAdd(Auth::SET_KEY_AUTH_TOKENS_PRE.$realAuth, $token);
            Sso::driver()->expire(Auth::SET_KEY_AUTH_TOKENS_PRE.$realAuth, Sso::expire());
            return Encryption::encrypt($token);
        }
        return false;
    }
}