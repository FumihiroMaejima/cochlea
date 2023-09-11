<?php

namespace App\Library\User;

use Illuminate\Support\Facades\Hash;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Hash\HashLibrary;
use App\Models\User;

class UserLibrary
{
    /**
     * validate user password by record.
     *
     * @param string $value
     * @return bool
     */
    public static function validateUserPassword(string $value, $user): bool
    {
        $pepper = HashLibrary::getPepper();
        $taget = $value. $user[User::SALT] . $pepper;
        $hashedvalue = $user[User::PASSWORD];
        // 現在のパスワードのチェック
        if (!Hash::check($taget, $user[User::PASSWORD])) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'hash check failed.'
            );
        }

        return true;
    }
}
