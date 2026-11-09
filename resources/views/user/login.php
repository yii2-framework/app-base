<?php

declare(strict_types=1);

use app\usecases\shared\view\YiiFormModel;
use app\usecases\user\LoginForm;
use UIAwesome\Html\Field\Field;
use UIAwesome\Html\Flow\P;
use UIAwesome\Html\Form\{Button, Form, InputCheckbox, InputHidden};
use UIAwesome\Html\Form\Values\{ButtonType, Method};
use UIAwesome\Html\Heading\H1;
use UIAwesome\Html\Interop\Block;
use UIAwesome\Html\Palpable\A;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var LoginForm $model Login form model.
 * @var View $this View component instance.
 */
$formModel = new YiiFormModel($model);
$this->title = 'Login';
?>
<?= H1::tag()->content($this->title)->render() ?>
<?= Form::tag()
    ->action('')
    ->method(Method::POST)
    ->html(
        InputHidden::tag()->name(Yii::$app->request->csrfParam)->value(Yii::$app->request->csrfToken)->render(),
        Field::tag()
            ->formModel($formModel)
            ->property('username')
            ->control('text')
            ->containerTag(Block::P)
            ->render(),
        Field::tag()
            ->formModel($formModel)
            ->property('password')
            ->control('password')
            ->containerTag(Block::P)
            ->render(),
        P::tag()->html(
            Field::tag()
                ->formModel($formModel)
                ->property('rememberMe')
                ->input(InputHidden::tag())
                ->containerTag(false)
                ->id(null)
                ->notLabel()
                ->value(0)
                ->render(),
            Field::tag()
                ->formModel($formModel)
                ->property('rememberMe')
                ->enclosedByLabel()
                ->input(InputCheckbox::tag()->value(true))
                ->containerTag(false)
                ->label('Remember me')
                ->render(),
        )->render(),
        P::tag()->html(
            Button::tag()->type(ButtonType::SUBMIT)->content('Login')->render(),
        )->render(),
    )
    ->render() ?>
<?= P::tag()->html(
        A::tag()->href(Url::to(['/user/request-password-reset']))->content('Forgot your password?')->render(),
    )->render() ?>
<?= P::tag()->html(
        A::tag()->href(Url::to(['/user/resend-verification-email']))->content('Resend verification email')->render(),
    )->render() ?>
