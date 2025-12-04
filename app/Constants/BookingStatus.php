<?php


namespace App\Constants;

class BookingStatus
{
    const PENDING = 'pending';
    const CONFIRMED = 'confirmed';
    const CANCELLED = 'cancelled';


    /**
     * جلب جميع الحالات
     */
    public static function all(): array
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::CANCELLED,

        ];
    }

    /**
     * التحقق من حالة صحيحة
     */
    public static function isValid(string $status): bool
    {
        return in_array($status, self::all());
    }
}
