<?php

declare(strict_types=1);

namespace app\tests\unit\usecases\shared\view;

use app\usecases\shared\view\{FieldMetadataInterface, YiiFormModel};
use Codeception\Test\Unit;
use Override;
use UIAwesome\Html\Field\Field;
use yii\base\Model;

/**
 * Unit tests for the Yii form-model rendering adapter.
 */
final class YiiFormModelTest extends Unit
{
    public function testCopiesYiiRenderingMetadataAndValidationState(): void
    {
        $model = new YiiFormModelStub();
        $model->email = 'ada@example.com';
        $model->addError('email', 'Email is already registered.');

        $formModel = new YiiFormModel($model);

        self::assertSame('Profile', $formModel->getModelName());
        self::assertSame(['email'], $formModel->getNames());
        self::assertSame('ada@example.com', $formModel->getValue('email'));
        self::assertSame('Email address', $formModel->getLabel('email'));
        self::assertSame('Used to sign in.', $formModel->getHint('email'));
        self::assertSame('name@example.com', $formModel->getPlaceholder('email'));
        self::assertSame(['class' => ['form-control']], $formModel->getFieldConfig('email'));
        self::assertSame('Email is already registered.', $formModel->getFirstError('email'));
    }

    public function testRendersYiiModelWithHtmlField(): void
    {
        $model = new YiiFormModelStub();
        $model->email = 'ada@example.com';

        $html = Field::tag()
            ->formModel(new YiiFormModel($model))
            ->property('email')
            ->control('email')
            ->render();

        self::assertStringContainsString('<label for="profile-email">Email address</label>', $html);
        self::assertStringContainsString('name="Profile[email]"', $html);
        self::assertStringContainsString('type="email"', $html);
        self::assertStringContainsString('value="ada@example.com"', $html);
        self::assertStringContainsString('Used to sign in.', $html);
    }
}

/**
 * Yii form model stub with custom rendering metadata.
 */
final class YiiFormModelStub extends Model implements FieldMetadataInterface
{
    public string $email = '';

    #[Override]
    public function attributeHints(): array
    {
        return ['email' => 'Used to sign in.'];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return ['email' => 'Email address'];
    }

    #[Override]
    public function formName(): string
    {
        return 'Profile';
    }

    public function getFieldConfigs(): array
    {
        return ['email' => ['class' => ['form-control']]];
    }

    public function getPlaceholders(): array
    {
        return ['email' => 'name@example.com'];
    }
}
