<?php

declare(strict_types=1);

namespace app\tests\support\fixtures;

use app\usecases\shared\user\User;
use yii\test\ActiveFixture;

/**
 * Provides user fixture data for authentication tests.
 */
class UserFixture extends ActiveFixture
{
    public $modelClass = User::class;
}
