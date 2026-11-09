<?php

declare(strict_types=1);

use app\usecases\shared\view\YiiFormModel;
use app\usecases\user\PasswordResetRequestForm;
use UIAwesome\Html\Field\Field;
use UIAwesome\Html\Flow\P;
use UIAwesome\Html\Form\{Button, Form, InputHidden};
use UIAwesome\Html\Form\Values\{ButtonType, Method};
use UIAwesome\Html\Heading\H1;
use UIAwesome\Html\Interop\Block;
use yii\web\View;

/**
 * @var PasswordResetRequestForm $model Password reset request model.
 * @var View $this View component instance.
 */
$formModel = new YiiFormModel($model);
$this->title = 'Request password reset';
?>
<?= H1::tag()->content($this->title)->render() ?>
<?= Form::tag()
    ->action('')
    ->method(Method::POST)
    ->html(
        InputHidden::tag()->name(Yii::$app->request->csrfParam)->value(Yii::$app->request->csrfToken)->render(),
        Field::tag()
            ->formModel($formModel)
            ->property('email')
            ->control('email')
            ->containerTag(Block::P)
            ->render(),
        P::tag()->html(
            Button::tag()->type(ButtonType::SUBMIT)->content('Send')->render(),
        )->render(),
    )
    ->render() ?>
