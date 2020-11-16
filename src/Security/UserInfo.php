<?php

namespace App\Security;

/**
 * Class UserInfo
 * A toolkit class to customize user information for Http Response. This is not an entity that represent a User
 *
 * @package App\Security
 */
class UserInfo
{
    /**
     * @param callable $isGranted
     * @param callable $getUser
     * @return array
     */
    public static function getUserInfo(callable $isGranted, callable $getUser)
    {
        // will be useful if we decide to return always 200 + the real Json content represented by isLoggedIn: 0|1
        $authenticated = $isGranted('IS_AUTHENTICATED_FULLY');
        $data = ['isLoggedIn' => (int)$authenticated,];

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
