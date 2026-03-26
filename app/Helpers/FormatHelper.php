<?php

if (! function_exists('formatCOP')) {
    /**
     * Format a COP (Colombian Peso) value with abbreviated notation.
     *
     * Returns short forms: $1.2B, $539M, $4.5K
     * The full value is available as a data attribute / title for tooltips.
     *
     * @param  int|float|null  $value
     * @param  int  $decimals  Decimal places for the abbreviated number
     * @return string  e.g. "$1.2B COP"
     */
    function formatCOP(int|float|null $value, int $decimals = 1): string
    {
        $value = (float) ($value ?? 0);

        if (abs($value) >= 1_000_000_000) {
            $short = round($value / 1_000_000_000, $decimals);
            // Remove trailing zero after decimal (e.g. 2.0 → 2)
            $formatted = rtrim(rtrim(number_format($short, $decimals, '.', ''), '0'), '.');
            return '$' . $formatted . 'B COP';
        }

        if (abs($value) >= 1_000_000) {
            $short = round($value / 1_000_000, $decimals);
            $formatted = rtrim(rtrim(number_format($short, $decimals, '.', ''), '0'), '.');
            return '$' . $formatted . 'M COP';
        }

        if (abs($value) >= 1_000) {
            $short = round($value / 1_000, $decimals);
            $formatted = rtrim(rtrim(number_format($short, $decimals, '.', ''), '0'), '.');
            return '$' . $formatted . 'K COP';
        }

        return '$' . number_format($value, 0, ',', '.') . ' COP';
    }
}

if (! function_exists('formatCOPFull')) {
    /**
     * Format a COP value in full notation for tooltips.
     *
     * @param  int|float|null  $value
     * @return string  e.g. "$1,234,567,890 COP"
     */
    function formatCOPFull(int|float|null $value): string
    {
        $value = (float) ($value ?? 0);
        return '$' . number_format($value, 0, ',', '.') . ' COP';
    }
}
