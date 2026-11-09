<?php

declare(strict_types=1);

use UIAwesome\Html\Flow\Main;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var string $content Inner view content to render inside the layout.
 * @var View $this View component instance.
 */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Html::encode(Yii::$app->language) ?>">
    <?= $this->render('head') ?>
    <body>
        <?php $this->beginBody() ?>
        <?= $this->render('component/menu') ?>
        <?= $this->render('component/alerts') ?>
        <?= Main::tag()->html($content) ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage();
