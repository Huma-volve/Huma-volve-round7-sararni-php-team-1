<?php

return [
    // Auth messages
    'register.success' => 'تم التسجيل بنجاح. يرجى التحقق من بريدك الإلكتروني باستخدام رمز OTP المرسل إليك.',
    'verify_otp.success' => 'تم التحقق من البريد الإلكتروني بنجاح.',
    'verify_otp.invalid' => 'رمز OTP غير صحيح أو منتهي الصلاحية.',
    'verify_otp.user_not_found' => 'المستخدم غير موجود.',
    'resend_otp.success' => 'تم إعادة إرسال رمز OTP بنجاح.',
    'resend_otp.too_many_attempts' => 'عدد كبير جداً من المحاولات. يرجى المحاولة مرة أخرى لاحقاً.',
    'login.success' => 'تم تسجيل الدخول بنجاح.',
    'login.invalid_credentials' => 'بيانات الدخول غير صحيحة.',
    'login.email_not_verified' => 'يرجى التحقق من بريدك الإلكتروني أولاً.',
    'logout.success' => 'تم تسجيل الخروج بنجاح.',
    'forgot_password.success' => 'تم إرسال رمز OTP إلى بريدك الإلكتروني. يرجى التحقق من صندوق الوارد.',
    'forgot_password.too_many_attempts' => 'عدد كبير جداً من المحاولات. يرجى المحاولة مرة أخرى لاحقاً.',
    'reset_password.success' => 'تم إعادة تعيين كلمة المرور بنجاح.',
    'reset_password.invalid_otp' => 'رمز OTP غير صحيح أو منتهي الصلاحية.',
    'reset_password.user_not_found' => 'المستخدم غير موجود.',
    'change_password.success' => 'تم تغيير كلمة المرور بنجاح.',
    'change_password.invalid' => 'كلمة المرور الحالية غير صحيحة.',

    // Google OAuth messages
    'google.auth_success' => 'تم المصادقة بنجاح.',
    'google.link_success' => 'تم ربط حساب Google بنجاح.',
    'google.unlink_success' => 'تم إلغاء ربط حساب Google بنجاح.',
    'google.oauth_error' => 'فشلت مصادقة OAuth.',
    'google.link_error' => 'فشل ربط حساب Google.',
    'google.unlink_error' => 'فشل إلغاء ربط حساب Google.',

    // User messages
    'profile.updated' => 'تم تحديث الملف الشخصي بنجاح.',

    // Error codes
    'error.user_not_found' => 'المستخدم غير موجود.',
    'error.invalid_otp' => 'رمز OTP غير صحيح أو منتهي الصلاحية.',
    'error.too_many_attempts' => 'عدد كبير جداً من المحاولات. يرجى المحاولة مرة أخرى لاحقاً.',
    'error.email_not_verified' => 'يرجى التحقق من بريدك الإلكتروني أولاً.',
    'error.invalid_password' => 'كلمة المرور الحالية غير صحيحة.',
    'error.oauth_error' => 'فشلت مصادقة OAuth.',
    'error.link_error' => 'فشل ربط الحساب.',
    'error.unlink_error' => 'فشل إلغاء ربط الحساب.',
    'error.unauthorized' => 'هذا الإجراء غير مصرح به. فقط المديرون وموظفو الدعم يمكنهم تنفيذ هذا الإجراء.',

    // Booking messages
    'booking.created' => 'تم إنشاء الحجز بنجاح.',
    'booking.confirmed' => 'تم تأكيد الحجز بنجاح.',
    'booking.cancelled' => 'تم إلغاء الحجز بنجاح.',
    'booking.not_found' => 'الحجز غير موجود.',
    'booking.tour_not_active' => 'هذه الجولة غير نشطة حالياً.',
    'booking.min_participants' => 'الحد الأدنى :min مشارك مطلوب.',
    'booking.max_participants' => 'الحد الأقصى :max مشارك مسموح.',
    'booking.date_not_available' => 'التاريخ المحدد غير متاح.',
    'booking.insufficient_slots' => 'الأماكن المتاحة غير كافية. المتاح: :available، المطلوب: :required.',
    'booking.available' => 'الجولة متاحة للتاريخ المحدد.',
    'booking.booking_failed' => 'فشل الحجز. يرجى المحاولة مرة أخرى.',
    'booking.invalid_status' => 'حالة الحجز غير صحيحة.',
    'booking.cannot_cancel' => 'لا يمكن إلغاء هذا الحجز.',

    // Review messages
    'review.created' => 'تم إرسال التقييم بنجاح. سيتم نشره بعد المراجعة.',
    'review.updated' => 'تم تحديث التقييم بنجاح.',
    'review.deleted' => 'تم حذف التقييم بنجاح.',
    'review.not_found' => 'التقييم غير موجود.',
    'review.booking_not_completed' => 'يمكنك تقييم الحجوزات المكتملة فقط.',
    'review.already_exists' => 'لقد قمت بإرسال تقييم لهذا الحجز بالفعل.',

    // Question messages
    'question.created' => 'تم إرسال السؤال بنجاح.',
    'question.answered' => 'تم الرد على السؤال بنجاح.',

    // Favorite messages
    'favorite.added' => 'تمت إضافة الجولة إلى المفضلة.',
    'favorite.removed' => 'تمت إزالة الجولة من المفضلة.',
];
