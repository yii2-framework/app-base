<?php

declare(strict_types=1);

namespace app\tests\unit\usecases\site;

use app\usecases\shared\view\PhpViewRenderer;
use app\usecases\site\HomeAction;
use Codeception\Test\Unit;
use Yii;
use yii\web\View;

/**
 * Unit tests for {@see HomeAction} home page rendering.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class HomeActionTest extends Unit
{
    public function testActionHome(): void
    {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $events = [];
        $view = Yii::$app->view;
        $handlers = [
            View::EVENT_BEGIN_PAGE => static function () use (&$events): void {
                $events[] = View::EVENT_BEGIN_PAGE;
            },
            View::EVENT_BEGIN_BODY => static function () use (&$events): void {
                $events[] = View::EVENT_BEGIN_BODY;
            },
            View::EVENT_END_BODY => static function () use (&$events): void {
                $events[] = View::EVENT_END_BODY;
            },
            View::EVENT_END_PAGE => static function () use (&$events): void {
                $events[] = View::EVENT_END_PAGE;
            },
        ];

        foreach ($handlers as $event => $handler) {
            $view->on($event, $handler);
        }

        try {
            $action = new HomeAction(new PhpViewRenderer());

            $response = $action->run();
        } finally {
            foreach ($handlers as $event => $handler) {
                $view->off($event, $handler);
            }
        }

        self::assertNotEmpty(
            $response,
            'Body must be non-empty.',
        );
        self::assertIsString(
            $response,
            'Body must render as a string.',
        );
        self::assertStringContainsString(
            '<!DOCTYPE html>',
            $response,
            'Output must be wrapped in the main layout.',
        );
        self::assertStringContainsString(
            '<meta charset="utf-8">',
            $response,
            'The Yii charset must be normalized to the value accepted by the HTML component.',
        );
        self::assertStringContainsString(
            '<main>',
            $response,
            'Layout `<main>` region must wrap the view content.',
        );
        self::assertStringContainsString(
            '<nav',
            $response,
            'Layout navigation partial must be rendered.',
        );
        self::assertSame(
            [View::EVENT_BEGIN_PAGE, View::EVENT_BEGIN_BODY, View::EVENT_END_BODY, View::EVENT_END_PAGE],
            $events,
            'Yii page and body lifecycle events must retain their order.',
        );
    }

    public function testLayoutEscapesApplicationMetadataAndFlashMessages(): void
    {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $applicationName = Yii::$app->name;
        $language = Yii::$app->language;
        Yii::$app->name = 'Application <script>alert(1)</script>';
        Yii::$app->language = 'en"><script>alert(2)</script>';
        Yii::$app->session->setFlash('success', 'Saved <strong>successfully</strong>.');
        Yii::$app->session->setFlash('errors', ['email' => ['Email <em>is invalid</em>.']]);

        try {
            $response = (new HomeAction(new PhpViewRenderer()))->run();
        } finally {
            Yii::$app->name = $applicationName;
            Yii::$app->language = $language;
        }

        self::assertIsString(
            $response,
            'Home action must render the layout as a string.',
        );
        self::assertStringContainsString(
            'Application &lt;script&gt;alert(1)&lt;/script&gt;',
            $response,
            'The application name must be escaped in the heading, title, and navigation brand.',
        );
        self::assertStringContainsString(
            'lang="en&quot;&gt;&lt;script&gt;alert(2)&lt;/script&gt;"',
            $response,
            'The application language must be escaped in the document attribute.',
        );
        self::assertStringContainsString(
            'class="flash flash-success"',
            $response,
            'Scalar flash messages must render through the alert component.',
        );
        self::assertStringContainsString(
            'Saved &lt;strong&gt;successfully&lt;/strong&gt;.',
            $response,
            'Scalar flash messages must remain escaped.',
        );
        self::assertMatchesRegularExpression(
            '/<ul(?=[^>]*class="flash flash-errors")(?=[^>]*role="alert")[^>]*>/',
            $response,
            'Validation errors must preserve the list-based alert markup.',
        );
        self::assertMatchesRegularExpression(
            '/<li>\s*Email &lt;em&gt;is invalid&lt;\/em&gt;\.\s*<\/li>/',
            $response,
            'Each validation error must be escaped inside its list item.',
        );
        self::assertStringNotContainsString('<script>alert(1)</script>', $response);
        self::assertStringNotContainsString('<script>alert(2)</script>', $response);
        self::assertStringNotContainsString('<strong>successfully</strong>', $response);
        self::assertStringNotContainsString('<em>is invalid</em>', $response);
    }

    protected function tearDown(): void
    {
        Yii::$app->session->removeAllFlashes();

        unset($_SERVER['REQUEST_URI'], $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_METHOD']);

        parent::tearDown();
    }
}
