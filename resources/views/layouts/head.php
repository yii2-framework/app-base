<?php

declare(strict_types=1);

use UIAwesome\Html\Metadata\{Meta, Title};
use yii\web\View;

/** @var View $this View component instance. */
$pageTitle = $this->title ?? Yii::$app->name;
?>
<head>
    <?= Meta::tag()->charset(strtolower(Yii::$app->charset)) ?>
    <?= Meta::tag()->name('viewport')->content('width=device-width, initial-scale=1') ?>
    <?= Title::tag()->content($pageTitle) ?>
    <?php $this->head() ?>
</head>
