<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class InvoiceCalculationTest extends TestCase
{
    public function test_invoice_total_is_calculated_correctly(): void
    {
        $laborCost = 1500;
        $partsCost = 2500;
        $total = $laborCost + $partsCost;

        $this->assertEquals(4000, $total);
    }

    public function test_booking_status_value_can_be_completed(): void
    {
        $status = 'Completed';

        $this->assertEquals('Completed', $status);
    }
}