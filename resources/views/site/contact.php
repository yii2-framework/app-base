<?php

declare(strict_types=1);

use app\usecases\shared\view\YiiFormModel;
use app\usecases\site\ContactForm;
use UIAwesome\Html\Field\Field;
use UIAwesome\Html\Flow\P;
use UIAwesome\Html\Form\{Button, Form, InputHidden};
use UIAwesome\Html\Form\Values\{ButtonType, Method};
use UIAwesome\Html\Heading\H1;
use UIAwesome\Html\Interop\Block;
use yii\web\View;

/**
 * @var ContactForm $model Contact form model.
 * @var View $this View component instance.
 */
$formModel = new YiiFormModel($model);
$this->title = 'Contact';
?>
<?= H1::tag()
    ->content($this->title)
    ->render() ?>
<?= Form::tag()
    ->action('')
    ->method(Method::POST)
    ->html(
        InputHidden::tag()
            ->name(Yii::$app->request->csrfParam)
            ->value(Yii::$app->request->csrfToken)
            ->render(),
        Field::tag()
            ->formModel($formModel)
            ->property('name')
            ->control('text')
            ->containerTag(Block::P)
            ->render(),
        Field::tag()
            ->formModel($formModel)
            ->property('email')
            ->control('email')
            ->containerTag(Block::P)
            ->render(),
        Field::tag()
            ->formModel($formModel)
            ->property('phone')
            ->control('text')
            ->containerTag(Block::P)
            ->render(),
        Field::tag()
            ->formModel($formModel)
            ->property('subject')
            ->control('text')
            ->containerTag(Block::P)
            ->render(),
        Field::tag()
            ->formModel($formModel)
            ->property('body')
            ->control('textarea')
            ->containerTag(Block::P)
            ->render(),
        P::tag()->html(
            Button::tag()->type(ButtonType::SUBMIT)->content('Send')->render(),
        )->render(),
    )
    ->render() ?>
