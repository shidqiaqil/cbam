<?php
// Temporary helper for tooltip formatting - will be inlined into DataCalculation.php

function buildSourceTooltip(array $sources, ?float $rawTotal = null, ?float $finalTotal = null, ?string $unitNote = null): string
{
    $html = '<ul class="mb-0">';

    foreach ($sources as $source) {
        $label = $source['label'];
        $value = $source['value'] ?? '—';
        $html .= '<li>' . $label . ': <strong>' . $value . '</strong></li>';
    }

    $html .= '</ul>';

    if ($rawTotal !== null || $finalTotal !== null) {
        $html .= '<hr class="my-1 opacity-25">';
        $html .= '<div class="text-end fw-bold small">';
        if ($rawTotal !== null) {
            $html .= 'Raw Sum: <strong>' . number_format($rawTotal, 3) . '</strong><br>';
        }
        if ($finalTotal !== null) {
            $html .= 'Final: <strong>' . number_format($finalTotal, 3) . '</strong>';
            if ($unitNote) {
                $html .= ' (' . $unitNote . ')';
            }
        }
        $html .= '</div>';
    }

    return $html;
}
