<?php

declare(strict_types=1);

namespace app\usecases\shared\view;

/**
 * Provides UI Awesome field metadata that has no native equivalent on a Yii form model.
 *
 * Yii models already expose labels, hints, values, and validation errors. Implement this interface only when a form
 * also needs control configuration or placeholders transferred to `ui-awesome/html-field`.
 */
interface FieldMetadataInterface
{
    /**
     * Returns fluent control configuration indexed by field name.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getFieldConfigs(): array;

    /**
     * Returns input placeholders indexed by field name.
     *
     * @return array<string, string>
     */
    public function getPlaceholders(): array;
}
