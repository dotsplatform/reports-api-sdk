<?php

declare(strict_types=1);

/**
 * Description of ResponseField.php
 *
 * Typed reads from a decoded JSON response. A field with another type than documented throws
 * ReportsUnexpectedResponseException, so a changed API fails loudly instead of filling DTOs with wrong values.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Support;

use Dotsplatform\ReportsApi\Exceptions\ReportsUnexpectedResponseException;

final class ResponseField
{
    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function string(array $data, string $key): string
    {
        $value = $data[$key] ?? null;
        if (! is_string($value)) {
            throw self::unexpectedType($key, 'string');
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function nullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw self::unexpectedType($key, 'string or null');
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function int(array $data, string $key): int
    {
        $value = $data[$key] ?? null;
        if (! is_int($value)) {
            throw self::unexpectedType($key, 'integer');
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function nullableInt(array $data, string $key): ?int
    {
        $value = $data[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (! is_int($value)) {
            throw self::unexpectedType($key, 'integer or null');
        }

        return $value;
    }

    /**
     * JSON numbers: whole numbers decode as int, the others as float.
     *
     * @param  array<array-key, mixed>  $data
     */
    public static function number(array $data, string $key): int|float
    {
        $value = self::nullableNumber($data, $key);
        if ($value === null) {
            throw self::unexpectedType($key, 'number');
        }

        return $value;
    }

    /**
     * JSON numbers: whole numbers decode as int, the others as float.
     *
     * @param  array<array-key, mixed>  $data
     */
    public static function nullableNumber(array $data, string $key): int|float|null
    {
        $value = $data[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return $value;
        }

        throw self::unexpectedType($key, 'number or null');
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function bool(array $data, string $key): bool
    {
        $value = $data[$key] ?? null;
        if (! is_bool($value)) {
            throw self::unexpectedType($key, 'boolean');
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    public static function array(array $data, string $key): array
    {
        $value = $data[$key] ?? null;
        if (! is_array($value)) {
            throw self::unexpectedType($key, 'object or array');
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>|null
     */
    public static function nullableArray(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (! is_array($value)) {
            throw self::unexpectedType($key, 'object, array or null');
        }

        return $value;
    }

    /**
     * A JSON object as a PHP array with string keys (an empty object decodes to an empty array).
     *
     * @param  array<array-key, mixed>  $data
     * @return array<string, mixed>
     */
    public static function object(array $data, string $key): array
    {
        $object = [];
        foreach (self::array($data, $key) as $objectKey => $value) {
            $object[(string) $objectKey] = $value;
        }

        return $object;
    }

    /**
     * A JSON object whose values are objects, keyed by string (metric keys, breakdown values).
     *
     * @param  array<array-key, mixed>  $data
     * @return array<string, array<array-key, mixed>>
     */
    public static function objectOfObjects(array $data, string $key): array
    {
        $objects = [];
        foreach (self::array($data, $key) as $objectKey => $value) {
            if (! is_array($value)) {
                throw self::unexpectedType($key.'.'.$objectKey, 'object');
            }

            $objects[(string) $objectKey] = $value;
        }

        return $objects;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return list<array<array-key, mixed>>
     */
    public static function listOfObjects(array $data, string $key): array
    {
        $items = [];
        foreach (self::array($data, $key) as $item) {
            if (! is_array($item)) {
                throw self::unexpectedType($key.'[]', 'object');
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return list<string>
     */
    public static function stringList(array $data, string $key): array
    {
        $items = [];
        foreach (self::array($data, $key) as $item) {
            if (! is_string($item)) {
                throw self::unexpectedType($key.'[]', 'string');
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return list<int>
     */
    public static function intList(array $data, string $key): array
    {
        $items = [];
        foreach (self::array($data, $key) as $item) {
            if (! is_int($item)) {
                throw self::unexpectedType($key.'[]', 'integer');
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<string, int>
     */
    public static function intMap(array $data, string $key): array
    {
        $map = [];
        foreach (self::array($data, $key) as $mapKey => $item) {
            if (! is_int($item)) {
                throw self::unexpectedType($key.'.'.$mapKey, 'integer');
            }

            $map[(string) $mapKey] = $item;
        }

        return $map;
    }

    private static function unexpectedType(string $key, string $expectedType): ReportsUnexpectedResponseException
    {
        return new ReportsUnexpectedResponseException(sprintf('The reports service response field "%s" is not a %s.', $key, $expectedType));
    }
}
