<?php

class TaxService {
    private const VAT_RATE = 0.07;

    /**
     * Calculate VAT and Subtotal from a grand total
     * Total = Subtotal + (Subtotal * VAT)
     * Total = Subtotal * (1 + VAT)
     * Subtotal = Total / (1 + VAT)
     */
    public static function calculateFromTotal($total) {
        $subtotal = $total / (1 + self::VAT_RATE);
        $vatAmount = $total - $subtotal;

        return [
            'subtotal' => round($subtotal, 2),
            'vat_amount' => round($vatAmount, 2),
            'total' => round($total, 2),
            'vat_rate' => self::VAT_RATE * 100
        ];
    }

    /**
     * Calculate VAT and Total from a subtotal
     */
    public static function calculateFromSubtotal($subtotal) {
        $vatAmount = $subtotal * self::VAT_RATE;
        $total = $subtotal + $vatAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'vat_amount' => round($vatAmount, 2),
            'total' => round($total, 2),
            'vat_rate' => self::VAT_RATE * 100
        ];
    }

    public static function formatMoney($amount) {
        return number_format($amount, 2, '.', ',');
    }
}
