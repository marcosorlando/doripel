<?php

if (!function_exists('doripelNormalizeDimension')) {
    function doripelNormalizeDimension(mixed $value): float
    {
        if (is_array($value)) {
            return 0.0;
        }

        $value = str_replace(',', '.', trim((string)$value));

        return is_numeric($value) ? (float)$value : 0.0;
    }
}

if (!function_exists('doripelLegacyProductVolumes')) {
    /**
     * @param array<string, mixed> $product
     * @return list<array{weight: float, depth: float, width: float, height: float}>
     */
    function doripelLegacyProductVolumes(array $product): array
    {
        $volumes = [
            [
                'weight' => doripelNormalizeDimension($product['pdt_dimension_weight'] ?? 0),
                'depth' => doripelNormalizeDimension($product['pdt_dimension_depth'] ?? 0),
                'width' => doripelNormalizeDimension($product['pdt_dimension_width'] ?? 0),
                'height' => doripelNormalizeDimension($product['pdt_dimension_heigth'] ?? 0),
            ],
        ];

        $secondVolume = [
            'weight' => doripelNormalizeDimension($product['pdt_dimension_weight_cx2'] ?? 0),
            'depth' => doripelNormalizeDimension($product['pdt_dimension_depth_cx2'] ?? 0),
            'width' => doripelNormalizeDimension($product['pdt_dimension_width_cx2'] ?? 0),
            'height' => doripelNormalizeDimension($product['pdt_dimension_heigth_cx2'] ?? 0),
        ];

        if (array_filter($secondVolume, static fn(float $value): bool => $value > 0)) {
            $volumes[] = $secondVolume;
        }

        return $volumes;
    }
}

if (!function_exists('doripelProductVolumes')) {
    /**
     * @param array<string, mixed> $product
     * @return list<array{weight: float, depth: float, width: float, height: float}>
     */
    function doripelProductVolumes(array $product, object $read): array
    {
        $productId = (int)($product['pdt_id'] ?? 0);
        if ($productId <= 0 || !defined('DB_PDT_VOLUMES_DORIPEL')) {
            return doripelLegacyProductVolumes($product);
        }

        try {
            $read->exeRead(DB_PDT_VOLUMES_DORIPEL, "WHERE pdt_id = :id ORDER BY volume_order ASC, volume_id ASC", "id={$productId}");
        } catch (Throwable) {
            return doripelLegacyProductVolumes($product);
        }

        if (!$read->getResult()) {
            return doripelLegacyProductVolumes($product);
        }

        $volumes = [];
        foreach ($read->getResult() as $volume) {
            $normalizedVolume = [
                'weight' => doripelNormalizeDimension($volume['volume_weight'] ?? 0),
                'depth' => doripelNormalizeDimension($volume['volume_depth'] ?? 0),
                'width' => doripelNormalizeDimension($volume['volume_width'] ?? 0),
                'height' => doripelNormalizeDimension($volume['volume_height'] ?? 0),
            ];

            if (!array_filter($normalizedVolume, static fn(float $value): bool => $value > 0)) {
                continue;
            }

            $volumes[] = $normalizedVolume;
        }

        return $volumes ?: doripelLegacyProductVolumes($product);
    }
}

if (!function_exists('doripelProductCubage')) {
    /**
     * @param list<array{weight: float, depth: float, width: float, height: float}> $volumes
     */
    function doripelProductCubage(array $volumes): float
    {
        $cubage = 0.0;

        foreach ($volumes as $volume) {
            $cubage += ($volume['height'] ?? 0.0) * ($volume['width'] ?? 0.0) * ($volume['depth'] ?? 0.0);
        }

        return $cubage;
    }
}
