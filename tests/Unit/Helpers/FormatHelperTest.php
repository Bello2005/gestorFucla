<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class FormatHelperTest extends TestCase
{
    // ── formatCOP ─────────────────────────────────────────────────────────

    public function test_format_cop_null_returns_zero(): void
    {
        $this->assertSame('$0 COP', formatCOP(null));
    }

    public function test_format_cop_small_value_below_thousand(): void
    {
        $this->assertSame('$500 COP', formatCOP(500));
    }

    public function test_format_cop_exact_zero(): void
    {
        $this->assertSame('$0 COP', formatCOP(0));
    }

    public function test_format_cop_thousands(): void
    {
        $this->assertSame('$4.5K COP', formatCOP(4500));
    }

    public function test_format_cop_exact_thousand(): void
    {
        $this->assertSame('$1K COP', formatCOP(1000));
    }

    public function test_format_cop_millions(): void
    {
        $this->assertSame('$1.5M COP', formatCOP(1_500_000));
    }

    public function test_format_cop_exact_million(): void
    {
        $this->assertSame('$1M COP', formatCOP(1_000_000));
    }

    public function test_format_cop_billions(): void
    {
        $this->assertSame('$2.3B COP', formatCOP(2_300_000_000));
    }

    public function test_format_cop_exact_billion(): void
    {
        $this->assertSame('$1B COP', formatCOP(1_000_000_000));
    }

    public function test_format_cop_removes_trailing_decimal_zero(): void
    {
        $this->assertSame('$2M COP', formatCOP(2_000_000));
    }

    public function test_format_cop_negative_value(): void
    {
        $this->assertSame('$-500K COP', formatCOP(-500_000));
    }

    public function test_format_cop_custom_decimals(): void
    {
        $result = formatCOP(1_234_000, 2);
        $this->assertSame('$1.23M COP', $result);
    }

    // ── formatCOPFull ─────────────────────────────────────────────────────

    public function test_format_cop_full_null_returns_zero(): void
    {
        $this->assertSame('$0 COP', formatCOPFull(null));
    }

    public function test_format_cop_full_small_value(): void
    {
        $this->assertSame('$500 COP', formatCOPFull(500));
    }

    public function test_format_cop_full_large_value_uses_period_as_thousands(): void
    {
        $this->assertSame('$1.234.567 COP', formatCOPFull(1_234_567));
    }

    public function test_format_cop_full_exact_million(): void
    {
        $this->assertSame('$1.000.000 COP', formatCOPFull(1_000_000));
    }
}
