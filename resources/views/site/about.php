<?php

declare(strict_types=1);

use UIAwesome\Html\Flow\P;
use UIAwesome\Html\Heading\H1;
use yii\web\View;

/** @var View $this View component instance. */
$this->title = 'About';
?>
<?= H1::tag()
    ->content($this->title)
    ->render() ?>
<?= P::tag()
    ->content('About page.')
    ->render();
