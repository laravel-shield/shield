<?php

namespace Shield\Shield\Support;

use Illuminate\Http\Request;

trait BasicAuth
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param string                   $username
     * @param string                   $password
     *
     * @return bool
     */
    public function checkBasic(Request $request, string $username, string $password): bool
    {
        if ($request->hasHeader('PHP-AUTH-USER') && $request->hasHeader('PHP-AUTH-PW')) {
            return $request->header('PHP-AUTH-USER') == $username && $request->header('PHP-AUTH-PW') == $password;
        }

        return false;
    }
}
