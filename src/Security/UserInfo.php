<?php

namespace App\Security;

/**
 * Class UserInfo
 * @package App\Security
 */
class UserInfo
{
    /**
     * @param callable $isGranted
     * @param callable $getUser
     * @return array
     */
    public static function getUserInfo(callable $isGranted, callable $getUser) {
        // will be usefull if we decide to return always 200 + the real Json content represented by isLoggedIn: 0|1
        $authenticated = $isGranted('IS_AUTHENTICATED_FULLY');
        $data = ['isLoggedIn' => (int)$authenticated, ];

        if ($authenticated) {
            $user = $getUser();
            $data['me'] = [
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ];
        }

        return $data;
    }
}
