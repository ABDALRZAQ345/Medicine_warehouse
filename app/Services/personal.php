<?php

namespace App\Services;

class personal
{
    /**
     * Create a new class instance.
     */
    public static array $order_status = ['repairing', 'sent', 'delivered'];

    public static array $payment_status = ['unpaid', 'paid'];

    public function __construct()
    {
        //
    }
}
