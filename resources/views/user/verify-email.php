<?php

declare(strict_types=1);

use app\usecases\shared\user\VerifyEmailForm;
use UIAwesome\Html\Flow\P;
use UIAwesome\Html\Form\{Button, Form, InputHidden};
use UIAwesome\Html\Form\Values\{ButtonType, Method};
use UIAwesome\Html\Heading\H1;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var VerifyEmailForm $model Verify email form model.
 * @var string $token Verification token preserved across the post/redirect cycle.
 * @var View $this View component instance.
 */
$this->title = 'Confirm email';
?>
<?= H1::tag()->content($this->title)->render() ?>
<?= P::tag()->content('Click the button below to confirm your email address.')->render() ?>
<?= Form::tag()
    ->action(Url::to(['/user/confirm-email', 'token' => $token]))
    ->method(Method::POST)
    ->html(
        InputHidden::tag()->name(Yii::$app->request->csrfParam)->value(Yii::$app->request->csrfToken)->render(),
        P::tag()->html(
            Button::tag()->type(ButtonType::SUBMIT)->content('Confirm')->render(),
        )->render(),
    )
    ->render() ?>
