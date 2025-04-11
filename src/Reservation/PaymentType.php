<?php

namespace App\Reservation;

enum PaymentType: string
{
    case PAYPAL = "PAYPAL";
    case STRIPE = "STRIPE";
}