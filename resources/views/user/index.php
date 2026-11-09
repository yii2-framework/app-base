<?php

declare(strict_types=1);

use app\usecases\user\UserSearch;
use UIAwesome\Html\Heading\H1;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var ActiveDataProvider $dataProvider Data provider for the users grid.
 * @var UserSearch $searchModel Search model bound to the filter row.
 * @var View $this View component instance.
 */
$this->title = 'Users';
?>
<?= H1::tag()->content($this->title)->render() ?>
<?= GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'username',
            'email',
            'status',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'filter' => false,
            ],
        ],
    ],
);
