<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property static array $defaultOptions
 */
trait HasOptions
{
    /**
     * Default daftar options (bisa di-override di Resource)
     * Contoh: ['status' => \App\Enums\UserStatus::class]
     */
    protected static array $defaultOptions = [];

    /**
     * Membuat array data + options dalam satu response.
     */
    public static function withOptions($resource, array $extraOptions = []): array
    {
        // Buat instance resource
        $instance = new static($resource);

        // Ambil default options dari Resource (jika ada)
        $options = method_exists($instance, 'getOptions')
            ? $instance->getOptions()
            : static::resolveDefaultOptions();

        // Gabungkan options default + tambahan manual
        $merged = array_merge($options, $extraOptions);

        return array_merge(['data' => $instance], $merged);
    }

    /**
     * Resolve default options array dari $defaultOptions property.
     */
    protected static function resolveDefaultOptions(): array
    {
        $options = [];

        foreach (static::$defaultOptions as $key => $source) {
            if (is_string($source) && enum_exists($source)) {
                $options["{$key}_options"] = $source::options();
            } elseif (is_callable($source)) {
                $options["{$key}_options"] = $source();
            } else {
                $options["{$key}_options"] = $source;
            }
        }

        return $options;
    }

    public static function onlyOptions(array $extraOptions = []): array
    {
        $instance = new static(null);

        $options = method_exists($instance, 'getOptions')
            ? $instance->getOptions()
            : static::resolveDefaultOptions();

        return array_merge($options, $extraOptions);
    }
}
