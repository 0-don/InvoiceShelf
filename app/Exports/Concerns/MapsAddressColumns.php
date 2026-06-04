<?php

namespace App\Exports\Concerns;

use App\Models\Address;

trait MapsAddressColumns
{
    /**
     * @return array<int, string>
     */
    protected static function addressRow(?Address $address): array
    {
        if (! $address) {
            return array_fill(0, 9, '');
        }

        return [
            $address->name ?? '',
            $address->address_street_1 ?? '',
            $address->address_street_2 ?? '',
            $address->city ?? '',
            $address->state ?? '',
            $address->zip ?? '',
            $address->country?->name ?? '',
            $address->country?->code ?? '',
            $address->phone ?? '',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected static function addressHeaders(string $prefix): array
    {
        return [
            "{$prefix}_name",
            "{$prefix}_street_1",
            "{$prefix}_street_2",
            "{$prefix}_city",
            "{$prefix}_state",
            "{$prefix}_zip",
            "{$prefix}_country",
            "{$prefix}_country_code",
            "{$prefix}_phone",
        ];
    }
}
