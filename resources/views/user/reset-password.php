<?php

declare(strict_types=1);

use app\usecases\shared\view\YiiFormModel;
use app\usecases\user\ResetPasswordForm;
use UIAwesome\Html\Field\Field;
use UIAwesome\Html\Flow\P;
use UIAwesome\Html\Form\{Button, Form, InputHidden};
use UIAwesome\Html\Form\Values\{ButtonType, Method};
use UIAwesome\Html\Heading\H1;
use UIAwesome\Html\Interop\Block;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var ResetPasswordForm $model Reset password form model.
 * @var string $token Password reset token preserved across the post/redirect cycle.
 * @var View $this View component instance.
 */
$formModel = new YiiFormModel($model);
$this->title = 'Reset password';
?>
<?= H1::tag()->content($this->title)->render() ?>
<?= Form::tag()
    ->action(Url::to(['/user/reset-password', 'token' => $token]))
    ->method(Method::POST)
    ->html(
        InputHidden::tag()->name(Yii::$app->request->csrfParam)->value(Yii::$app->request->csrfToken)->render(),
        Field::tag()
            ->formModel($formModel)
            ->property('password')
            ->control('password')
            ->containerTag(Block::P)
            ->label('New password')
            ->render(),
        P::tag()->html(
            Button::tag()->type(ButtonType::SUBMIT)->content('Save')->render(),
        )->render(),
    )
    ->render() ?>
