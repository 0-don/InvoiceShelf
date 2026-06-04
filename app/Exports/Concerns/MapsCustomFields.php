<?php

namespace App\Exports\Concerns;

use App\Exports\Support\ExportFormatter;
use Illuminate\Database\Eloquent\Model;

trait MapsCustomFields
{
    protected static function customFieldsToJson(Model $model): string
    {
        if (! $model->relationLoaded('fields') || $model->fields->isEmpty()) {
            return '[]';
        }

        $fields = $model->fields
            ->map(fn ($field) => array_filter([
                'custom_field_id' => $field->custom_field_id,
                'label' => $field->customField?->label ?? $field->customField?->name,
                'type' => $field->type,
                'value' => $field->default_answer,
            ]))
            ->values()
            ->all();

        return ExportFormatter::json($fields);
    }
}
