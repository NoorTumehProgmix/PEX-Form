<?php

namespace Juzaweb\Backend\Models;

use Spatie\TranslationLoader\LanguageLine as BaseLanguageLine;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class LanguageLine extends BaseLanguageLine
{
    /**
     * Get the translations for a given group and locale.
     *
     * @param string $group
     * @param string $locale
     * @param string|null $namespace
     * @return array
     */
    public static function getTranslationsForGroup(string $locale, string $group, $namespace = null): array
    {
        return Cache::store('file')
            ->rememberForever(
                static::getCacheKey($group, $locale, $namespace),
                function () use ($group, $locale, $namespace) {
                    return static::query()
                        ->where('group', $group)
                        ->where('namespace', $namespace)
                        ->get()
                        ->reduce(function ($lines, self $languageLine) use ($group, $locale) {
                            $translation = $languageLine->getTranslation($locale);

                            if ($translation !== null && $group === '*') {
                                // Make a flat array when returning json translations
                                $lines[strtolower($languageLine->key)] = $translation;
                            } elseif ($translation !== null && $group !== '*') {
                                // Make a nesetd array when returning normal translations
                                Arr::set($lines, strtolower($languageLine->key), $translation);
                            }

                            return $lines;
                        }) ?? [];
                }
            );
    }
}
