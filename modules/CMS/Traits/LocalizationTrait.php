<?php

namespace Juzaweb\CMS\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;


trait LocalizationTrait
{

    public static function bootLocalizationTrait()
    {
        static::creating(function (Model $model) {
            $model->prepareLocalizationFields();
        });

        static::updating(function (Model $model) {
            $model->updateLocalizationFields();
        });
    }

    protected function prepareLocalizationFields(): void
    {
        foreach ($this->localizationFields as $field) {
            $locales = config('app.locales');
            $names = [];
            $names[Lang::locale()] = $this->attributes[$field] ?? null;
            foreach ($locales as $key => $locale) {
                if (!isset($names[$key])) {
                    $names[$key] = $this->attributes[$field] ?? null;
                }
            }
            $this->{$field} = json_encode($names);
        }
    }

    protected function updateLocalizationFields(): void
    {
        foreach ($this->localizationFields as $field) {
            if (is_string($this->attributes[$field]) && json_decode($this->attributes[$field]) == null) {
                $locale = Lang::locale();
                $currentValue = $this->getRawOriginal($field);
                $valueArray = json_decode($currentValue, true) ?? [];
                $valueArray[$locale] = $this->attributes[$field] ?? null;
                $this->{$field} = json_encode($valueArray);
            }
        }
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if (in_array($key, $this->localizationFields)) {
            $localizedValues = json_decode($value, true);
            if (is_array($localizedValues)) {
                $locale = Lang::locale();
                return $localizedValues[$locale] ?? $localizedValues['en'] ?? '';
            }
        }

        return $value;
    }

    public function __get($key)
    {
        $value = parent::getAttribute($key);
        if (in_array($key, $this->localizationFields)) {
            $localizedValues = json_decode($value, true);
            if (is_array($localizedValues)) {
                $locale = Lang::locale();
                return $localizedValues[$locale] ?? $localizedValues['en'] ?? '';
            }
        }

        return $value;
    }
}
