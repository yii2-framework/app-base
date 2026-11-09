<?php

declare(strict_types=1);

namespace app\tests\acceptance;

use app\tests\support\AcceptanceTester;
use app\tests\support\fixtures\UserFixture;
use app\usecases\shared\user\User;

/**
 * Acceptance tests for the user-area access control and verb constraints across standalone action slices.
 */
final class UserAccessCest
{
    /**
     * @return array{user: array{class: string, dataFile: string}}
     */
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                // @phpstan-ignore binaryOp.invalid
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function authenticatedUserCanReachResetPasswordWithToken(AcceptanceTester $I): void
    {
        $this->loginAs($I, 'admin');

        $owner = User::findByUsername('okirlin');

        $I->assertNotNull(
            $owner,
            "Expected fixture user 'okirlin' to exist.",
        );
        $I->amOnRoute('user/reset-password', ['token' => $owner->password_reset_token]);
        $I->dontSeeResponseCodeIs(403);
    }

    public function authenticatedUserCanReachVerifyEmailWithToken(AcceptanceTester $I): void
    {
        $this->loginAs($I, 'admin');

        $owner = User::findOne(['username' => 'test.test']);

        $I->assertNotNull(
            $owner,
            "Expected fixture user 'test.test' to exist.",
        );
        $I->amOnRoute('user/verify-email', ['token' => $owner->verification_token]);
        $I->dontSeeResponseCodeIs(403);
    }

    public function authenticatedUserIsRedirectedHomeFromLogin(AcceptanceTester $I): void
    {
        $this->loginAs($I, 'admin');

        $I->amOnRoute('user/login');
        $I->seeResponseCodeIs(200);
        $I->seeInCurrentUrl('/');
        $I->dontSeeInCurrentUrl('user/login');
    }

    public function authenticatedUserIsRedirectedHomeFromSignup(AcceptanceTester $I): void
    {
        $this->loginAs($I, 'admin');

        $I->amOnRoute('user/signup');
        $I->seeResponseCodeIs(200);
        $I->seeInCurrentUrl('/');
        $I->dontSeeInCurrentUrl('user/signup');
    }

    public function getOnConfirmEmailIsRejected(AcceptanceTester $I): void
    {
        $owner = User::findOne(['username' => 'test.test']);

        $I->assertNotNull(
            $owner,
            "Expected fixture user 'test.test' to exist.",
        );
        $I->amOnRoute('user/confirm-email', ['token' => $owner->verification_token]);
        $I->seeResponseCodeIs(405);
    }

    public function getVerbIsRejectedOnLogout(AcceptanceTester $I): void
    {
        $this->loginAs($I, 'admin');

        $I->amOnRoute('user/logout');
        $I->seeResponseCodeIs(405);
    }

    public function guestIsRedirectedFromUserIndex(AcceptanceTester $I): void
    {
        $I->amOnRoute('user/index');
        $I->seeInCurrentUrl('user/login');
        $I->seeResponseCodeIs(200);
    }

    public function nonAdminGetsForbiddenOnUserIndex(AcceptanceTester $I): void
    {
        $this->loginAs($I, 'okirlin');

        $I->amOnRoute('user/index');
        $I->seeResponseCodeIs(403);
    }

    public function unsupportedVerbIsRejectedOnContact(AcceptanceTester $I): void
    {
        $I->sendAjaxRequest('PUT', '/site/contact');
        $I->seeResponseCodeIs(405);
    }

    public function unsupportedVerbIsRejectedOnGuestForm(AcceptanceTester $I): void
    {
        $I->sendAjaxRequest('PUT', '/user/login');
        $I->seeResponseCodeIs(405);
    }

    public function unsupportedVerbIsRejectedOnResetPassword(AcceptanceTester $I): void
    {
        $I->sendAjaxRequest('PUT', '/user/reset-password?token=unused');
        $I->seeResponseCodeIs(405);
    }

    /**
     * Resolves a fixture user by username, fails fast if missing, and logs them in.
     *
     * Centralizes the `null`-guard so a missing or renamed fixture row surfaces as a clear test failure instead of a
     * confusing `amLoggedInAs(null)` downstream error.
     */
    private function loginAs(AcceptanceTester $I, string $username): void
    {
        $user = User::findByUsername($username);

        $I->assertNotNull(
            $user,
            "Expected fixture user '{$username}' to exist.",
        );
        $I->amLoggedInAs($user);
    }
}
