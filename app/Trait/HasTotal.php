<?php

namespace App\Trait;

trait HasTotal
{

    public static function computeTotalCharge($get, $set): void
    {
        $fee = floatval(str_replace(',', '', $get('fee') ?? 0));
        $followUpFees = floatval(str_replace(',', '', $get('follow_up_fees') ?? 0));
        $procedureFee = floatval(str_replace(',', '', $get('procedure_fee') ?? 0));
        $discount = floatval($get('discount') ?? 0);

        // dd($fee, $followUpFees, $procedureFee);
        $subtotal = $fee + $followUpFees + $procedureFee;

        // Apply discount if present
        $total = static::getTotal($fee, $followUpFees, $procedureFee, $discount);

        $set('Total', number_format($total, 2, '.', ''));
    }
    
    public static function getTotal($fee, $follow_up_fees, $procedure_fee, $discount)
    {
        $subtotal = $fee + $follow_up_fees + $procedure_fee;
        $deduction = $subtotal * ($discount / 100);
        $total = $subtotal - $deduction;

        return $total;
    }
}
