<?php

namespace App\Reservation;

enum ReservationStatus: string
{
    case CART = 'CART';
    case PAID = 'PAID';
}