<?php

declare(strict_types=1);

namespace app\tests\unit\usecases\site;

use app\usecases\shared\view\PhpViewRenderer;
use app\usecases\site\{ContactAction, ContactForm};
use Codeception\Test\Unit;
use RuntimeException;
use Yii;
use yii\mail\{BaseMailer, MailEvent};

/**
 * Unit tests for {@see ContactAction} form handling and mailer dispatch.
 */
final class ContactActionTest extends Unit
{
    public function testActionContactGet(): void
    {
        $_SERVER['REQUEST_URI'] = '/site/contact';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $action = new ContactAction(new PhpViewRenderer(), Yii::$app->mailer);

        $response = $action->run();

        self::assertNotEmpty(
            $response,
            'GET must render the form.',
        );
        self::assertIsString(
            $response,
            'GET must render the form as a string.',
        );
        self::assertStringContainsString('name="ContactForm[name]"', $response);
        self::assertStringContainsString('id="contactform-email"', $response);
        self::assertStringContainsString('type="email"', $response);
        self::assertStringContainsString('placeholder="(999) 999-9999"', $response);
        self::assertStringContainsString('rows="6"', $response);
        self::assertStringContainsString('type="submit"', $response);
    }

    public function testActionContactPostMailerFailure(): void
    {
        $_SERVER['REQUEST_URI'] = '/site/contact';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        Yii::$app->request->setBodyParams(
            [
                'ContactForm' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'phone' => '(555) 123-4567',
                    'subject' => 'Test Subject',
                    'body' => 'Test body content.',
                ],
            ],
        );

        $handler = static function (MailEvent $event): void {
            $event->isValid = false;
        };

        Yii::$app->mailer->on(BaseMailer::EVENT_BEFORE_SEND, $handler);

        try {
            $action = new ContactAction(new PhpViewRenderer(), Yii::$app->mailer);
            $response = $action->run();
        } finally {
            Yii::$app->mailer->off(BaseMailer::EVENT_BEFORE_SEND, $handler);
        }

        self::assertNotEmpty(
            $response,
            'Redirect on mailer veto must produce a response.',
        );
        self::assertTrue(
            Yii::$app->session->hasFlash('error'),
            'Error flash must be set.',
        );
        self::assertFalse(
            Yii::$app->session->hasFlash('success'),
            'Success flash must be absent.',
        );
    }

    public function testActionContactPostMailerThrows(): void
    {
        $_SERVER['REQUEST_URI'] = '/site/contact';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        Yii::$app->request->setBodyParams(
            [
                'ContactForm' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'phone' => '(555) 123-4567',
                    'subject' => 'Test Subject',
                    'body' => 'Test body content.',
                ],
            ],
        );

        $handler = static function (MailEvent $event): never {
            throw new RuntimeException('Simulated mailer transport exception.');
        };

        Yii::$app->mailer->on(BaseMailer::EVENT_BEFORE_SEND, $handler);

        try {
            $action = new ContactAction(new PhpViewRenderer(), Yii::$app->mailer);
            $response = $action->run();
        } finally {
            Yii::$app->mailer->off(BaseMailer::EVENT_BEFORE_SEND, $handler);
        }

        self::assertNotEmpty(
            $response,
            'Throwable must be caught and yield a response.',
        );
        self::assertTrue(
            Yii::$app->session->hasFlash('error'),
            'Error flash must be set.',
        );
        self::assertFalse(
            Yii::$app->session->hasFlash('success'),
            'Success flash must be absent.',
        );
    }

    public function testActionContactPostSuccess(): void
    {
        $_SERVER['REQUEST_URI'] = '/site/contact';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        Yii::$app->request->setBodyParams(
            [
                'ContactForm' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'phone' => '(555) 123-4567',
                    'subject' => 'Test Subject',
                    'body' => 'Test body content.',
                ],
            ],
        );

        $action = new ContactAction(new PhpViewRenderer(), Yii::$app->mailer);

        $response = $action->run();

        self::assertNotEmpty(
            $response,
            'Successful submit must produce a redirect response.',
        );
    }

    public function testActionContactPostValidationErrors(): void
    {
        $_SERVER['REQUEST_URI'] = '/site/contact';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        Yii::$app->request->setBodyParams(
            [
                'ContactForm' => [
                    'name' => '',
                    'email' => '',
                    'phone' => '',
                    'subject' => '',
                    'body' => '',
                ],
            ],
        );

        $action = new ContactAction(new PhpViewRenderer(), Yii::$app->mailer);

        $response = $action->run();

        self::assertNotEmpty(
            $response,
            'Validation failure must produce a redirect response.',
        );
        self::assertTrue(
            Yii::$app->session->hasFlash('errors'),
            'Errors flash must capture form errors.',
        );
        self::assertFalse(
            Yii::$app->session->hasFlash('success'),
            'Success flash must be absent.',
        );
    }

    public function testContactViewEscapesPrefilledValues(): void
    {
        $_SERVER['REQUEST_URI'] = '/site/contact';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $model = new ContactForm();
        $model->name = '" autofocus onfocus="alert(1)';
        $model->body = '<strong>Message</strong>';

        $response = (new PhpViewRenderer())->render('site/contact', ['model' => $model]);

        self::assertStringContainsString(
            'value="&quot; autofocus onfocus=&quot;alert(1)"',
            $response,
            'Input values must be escaped as attributes.',
        );
        self::assertStringContainsString(
            '&lt;strong&gt;Message&lt;/strong&gt;',
            $response,
            'Textarea values must be escaped as content.',
        );
        self::assertStringNotContainsString('<strong>Message</strong>', $response);
    }

    protected function tearDown(): void
    {
        Yii::$app->request->setBodyParams([]);
        Yii::$app->session->removeAllFlashes();

        unset($_SERVER['REQUEST_URI'], $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_METHOD']);

        parent::tearDown();
    }
}
