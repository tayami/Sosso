<?php

namespace Tayami\Sosso\Lib;

use Tayami\Sosso\Core\Sso;

class Auth
{
    const STRING_KEY_AUTH_PRE        = 'so_sso:auth:';               // 登录auth对应用户信息
    const SET_KEY_AUTH_TOKENS_PRE    = 'so_sso:auth_tokens:';        // auth_key对应令牌池
    const HASH_KEY_TOKEN_TO_AUTH_PRE = 'so_sso:token_to_auth:';      // 令牌对应登录auth_key

    // 登录cookie名称
    const AUTH_COOKIE_NAME = 'so_sso_auth';

    public static function localGetAuthInfo($realAuth = null)
    {
        $realAuth = $realAuth ? $realAuth : self::localGetRealAuthKey();
        return self::realAuthToInfo($realAuth);
    }

    public static function auth($info)
    {
        $realAuth      = Encryption::randStr();
        $encryptedText = Encryption::encrypt($realAuth);

        // 写cookie,并记录cookie明文对应用户信息
        setcookie(self::AUTH_COOKIE_NAME, $encryptedText, time()+Sso::expire());
        Sso::driver()->setex(self::STRING_KEY_AUTH_PRE.$realAuth, Sso::expire(), json_encode($info));
    }

    public static function cancelAuth()
    {
        if($realAuth = self::localGetRealAuthKey()) {
            self::delAuthCookie();
            self::delAuth($realAuth);
            self::delTokensByAuth($realAuth);
        }
    }

    public static function localGetRealAuthKey()
    {
        $authCookie = isset($_COOKIE[self::AUTH_COOKIE_NAME]) ? $_COOKIE[self::AUTH_COOKIE_NAME] : null;
        if($authCookie) {
            return Encryption::decrypt($_COOKIE[self::AUTH_COOKIE_NAME]);
        }
        return null;
    }

    public static function realAuthToInfo($realAuth)
    {
        $info = Sso::driver()->get(self::STRING_KEY_AUTH_PRE.$realAuth);
        if($infoArr = json_decode($info, true)) {
            return $infoArr;
        }
        return null;
    }

    private static function delAuthCookie()
    {
        return setcookie(self::AUTH_COOKIE_NAME, '', time()-1);
    }

    private static function delAuth($realAuth)
    {
        return Sso::driver()->del(self::STRING_KEY_AUTH_PRE.$realAuth);
    }

    private static function delTokensByAuth($realAuth)
    {
        $tokenList = Sso::driver()->sMembers(self::SET_KEY_AUTH_TOKENS_PRE.$realAuth);
        $delCount  = 0;

        foreach($tokenList as $token) {
            if(Sso::driver()->del(Auth::HASH_KEY_TOKEN_TO_AUTH_PRE.$token)) {
                $delCount++;
            }
        }

        Sso::driver()->del(self::SET_KEY_AUTH_TOKENS_PRE.$realAuth);
        return $delCount;
    }
}