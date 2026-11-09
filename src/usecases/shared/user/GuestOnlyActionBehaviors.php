<?php

declare(strict_types=1);

namespace app\usecases\shared\user;

use app\usecases\shared\filters\CsrfValidationFilter;
use Yii;
use yii\filters\{AccessControl, VerbFilter};

/**
 * Provides the shared {@see \yii\base\Action::behaviors()} for standalone user slices reserved to anonymous visitors
 * (login, signup, password reset request, verification email resend): an {@see AccessControl} rule restricting access
 * to guests, an HTTP verb constraint, and {@see CsrfValidationFilter} guarding unsafe submissions.
 *
 * Authenticated users hitting the slice are redirected to the home page via `denyCallback`; anonymous users pass
 * through.
 */
trait GuestOnlyActionBehaviors
{
    /**
     * Composes the access rule and CSRF guard shared by guest-only form actions.
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => static function (): void {
                            Yii::$app->response->redirect(Yii::$app->homeUrl);
                        },
                    ],
                    ['allow' => true, 'roles' => ['?']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['*' => ['GET', 'POST']],
            ],
            'csrf' => CsrfValidationFilter::class,
        ];
    }
}
