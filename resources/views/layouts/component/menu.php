<?php

declare(strict_types=1);

use UIAwesome\Html\Core\Component\{Item, Menu, NavBar};
use UIAwesome\Html\Form\{Button, Form, InputHidden};
use UIAwesome\Html\Form\Values\{ButtonType, Method};
use UIAwesome\Html\Phrasing\Span;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this View component instance. */
$user = Yii::$app->user;

$canViewUsers = $user->isGuest === false && $user->can('viewUsers');
$logoutForm = $user->isGuest === false
    ? Form::tag()
        ->action(Url::to(['/user/logout']))
        ->method(Method::POST)
        ->html(
            InputHidden::tag()
                ->name(Yii::$app->request->csrfParam)
                ->value(Yii::$app->request->csrfToken),
            Button::tag()
                ->type(ButtonType::SUBMIT)
                ->content('Logout'),
        )
    : '';
?>
<?= NavBar::tag()
    ->brandText(Span::tag()->content(Yii::$app->name))
    ->brandLink(Url::to(['/site/home']))
    ->menu(
        Menu::tag()->items(
            Item::tag()
                ->label('About')
                ->link(Url::to(['/site/about'])),
            Item::tag()
                ->label('Contact')
                ->link(Url::to(['/site/contact'])),
            Item::tag()
                ->label('Sign up')
                ->link(Url::to(['/user/signup']))
                ->visible($user->isGuest),
            Item::tag()
                ->label('Login')
                ->link(Url::to(['/user/login']))
                ->visible($user->isGuest),
            Item::tag()
                ->label('Users')
                ->link(Url::to(['/user/index']))
                ->visible($canViewUsers),
        ),
    )
    ->suffix($logoutForm);
