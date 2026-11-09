<?php

declare(strict_types=1);

namespace app\usecases\shared\view;

use Override;
use UIAwesome\FormModel\BaseFormModel;
use UIAwesome\Model\Attribute\DoNotCollect;
use yii\base\Model;

use function get_debug_type;

/**
 * Adapts a Yii form model snapshot to the form-model contract consumed by `ui-awesome/html-field`.
 *
 * Values, labels, hints, and validation errors are copied when the adapter is created. The original Yii model remains
 * responsible for request loading and validation; this adapter is intentionally limited to rendering that state.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class YiiFormModel extends BaseFormModel
{
    /**
     * Fluent control configuration indexed by field name.
     *
     * @var array<string, array<string, mixed>>
     */
    #[DoNotCollect]
    private readonly array $fieldConfigs;
    /**
     * Yii attribute hints indexed by field name.
     *
     * @var array<string, string>
     */
    #[DoNotCollect]
    private readonly array $hints;
    /**
     * Yii attribute labels indexed by field name.
     *
     * @var array<string, string>
     */
    #[DoNotCollect]
    private readonly array $labels;
    /**
     * Yii form name used to generate input names and identifiers.
     */
    #[DoNotCollect]
    private readonly string $modelName;
    /**
     * Input placeholders indexed by field name.
     *
     * @var array<string, string>
     */
    #[DoNotCollect]
    private readonly array $placeholders;

    public function __construct(Model $model)
    {
        $this->fieldConfigs = $model instanceof FieldMetadataInterface ? $model->getFieldConfigs() : [];
        $this->hints = $model->attributeHints();
        $this->labels = $model->attributeLabels();
        $this->modelName = $model->formName();
        $this->placeholders = $model instanceof FieldMetadataInterface ? $model->getPlaceholders() : [];

        /** @var array<string, mixed> $values */
        $values = $model->getAttributes();

        foreach ($values as $attribute => $value) {
            $this->add($attribute, get_debug_type($value));
        }

        $this->load([$this->modelName => $values]);

        /** @var array<string, array<int, string>> $errors */
        $errors = $model->getErrors();

        $this->setErrors($errors);
    }

    #[Override]
    public function getFieldConfigs(): array
    {
        return $this->fieldConfigs;
    }

    #[Override]
    public function getHints(): array
    {
        return $this->hints;
    }

    #[Override]
    public function getLabels(): array
    {
        return $this->labels;
    }

    #[Override]
    public function getModelName(): string
    {
        return $this->modelName;
    }

    #[Override]
    public function getPlaceholders(): array
    {
        return $this->placeholders;
    }
}
