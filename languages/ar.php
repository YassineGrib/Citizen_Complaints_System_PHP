<?php
/**
 * Arabic Language File
 *
 * This file contains all the translations for the Arabic language.
 */

$translations = [
    // General
    'app_name' => 'نظام شكاوى المواطنين',
    'home' => 'الرئيسية',
    'submit_complaint' => 'تقديم شكوى',
    'track_complaint' => 'تتبع شكوى',
    'admin_login' => 'تسجيل دخول المسؤول',
    'logout' => 'تسجيل الخروج',
    'dark_mode' => 'الوضع الداكن',
    'light_mode' => 'الوضع الفاتح',
    'language' => 'اللغة',

    // Homepage
    'welcome_message' => 'مرحبًا بك في نظام شكاوى المواطنين',
    'welcome_subtitle' => 'منصة إلكترونية لتقديم ومتابعة شكاوى المواطنين',
    'get_started' => 'ابدأ الآن',
    'how_it_works' => 'كيف يعمل',
    'step_1' => 'قدم شكواك',
    'step_1_desc' => 'املأ نموذج الشكوى بتفاصيلك ومعلومات الشكوى',
    'step_2' => 'استلم رقم التتبع',
    'step_2_desc' => 'ستحصل على رقم تتبع فريد لشكواك',
    'step_3' => 'تتبع شكواك',
    'step_3_desc' => 'استخدم رقم التتبع للتحقق من حالة شكواك',
    'step_4' => 'احصل على إشعارات',
    'step_4_desc' => 'تلقى إشعارات بالبريد الإلكتروني عند تحديث حالة شكواك',

    // Complaint Form
    'complaint_form' => 'نموذج الشكوى',
    'personal_info' => 'المعلومات الشخصية',
    'complaint_info' => 'معلومات الشكوى',
    'name' => 'الاسم الكامل',
    'email' => 'البريد الإلكتروني',
    'phone' => 'رقم الهاتف',
    'complaint_type' => 'نوع الشكوى',
    'subject' => 'الموضوع',
    'description' => 'الوصف',
    'location' => 'الموقع',
    'attachment' => 'مرفق (اختياري)',
    'attachment_help' => 'قم بتحميل صور أو مستندات متعلقة بشكواك (الحد الأقصى: 5 ميجابايت)',
    'submit' => 'إرسال',
    'reset' => 'إعادة تعيين',

    // Complaint Types
    'select_type' => 'اختر النوع',
    'roads' => 'الطرق والبنية التحتية',
    'lighting' => 'الإنارة العامة',
    'parks' => 'الحدائق العامة',
    'sports' => 'المرافق الرياضية',
    'waste' => 'إدارة النفايات',
    'water' => 'المياه والصرف الصحي',
    'noise' => 'التلوث الضوضائي',
    'other' => 'أخرى',

    // Tracking
    'tracking_title' => 'تتبع شكواك',
    'tracking_id' => 'رقم التتبع',
    'track' => 'تتبع',
    'complaint_details' => 'تفاصيل الشكوى',
    'status' => 'الحالة',
    'submission_date' => 'تاريخ التقديم',
    'last_update' => 'آخر تحديث',
    'admin_response' => 'رد المسؤول',

    // Status
    'new' => 'جديدة',
    'in_progress' => 'قيد المعالجة',
    'resolved' => 'تم الحل',
    'rejected' => 'مرفوضة',

    // Admin
    'admin_dashboard' => 'لوحة تحكم المسؤول',
    'all_complaints' => 'جميع الشكاوى',
    'new_complaints' => 'الشكاوى الجديدة',
    'in_progress_complaints' => 'قيد المعالجة',
    'resolved_complaints' => 'تم حلها',
    'rejected_complaints' => 'مرفوضة',
    'view' => 'عرض',
    'update_status' => 'تحديث الحالة',
    'add_response' => 'إضافة رد',
    'response' => 'الرد',
    'save' => 'حفظ',
    'cancel' => 'إلغاء',
    'search' => 'بحث',
    'no_complaints' => 'لم يتم العثور على شكاوى',

    // Login
    'login' => 'تسجيل الدخول',
    'email' => 'البريد الإلكتروني',
    'password' => 'كلمة المرور',
    'remember_me' => 'تذكرني',
    'forgot_password' => 'نسيت كلمة المرور؟',

    // Messages
    'complaint_submitted' => 'تم تقديم شكواك بنجاح!',
    'tracking_id_message' => 'رقم التتبع الخاص بك هو:',
    'complaint_not_found' => 'لم يتم العثور على الشكوى. يرجى التحقق من رقم التتبع الخاص بك.',
    'status_updated' => 'تم تحديث الحالة بنجاح!',
    'response_added' => 'تمت إضافة الرد بنجاح!',
    'login_failed' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
    'login_success' => 'تم تسجيل الدخول بنجاح!',
    'logout_success' => 'تم تسجيل الخروج بنجاح!',
    'error' => 'حدث خطأ. يرجى المحاولة مرة أخرى.',

    // Validation
    'required' => 'هذا الحقل مطلوب.',
    'invalid_email' => 'يرجى إدخال عنوان بريد إلكتروني صالح.',
    'invalid_phone' => 'يرجى إدخال رقم هاتف صالح.',
    'file_too_large' => 'الملف كبير جدًا. الحجم الأقصى هو 5 ميجابايت.',
    'invalid_file_type' => 'نوع الملف غير صالح. الأنواع المسموح بها: jpg، jpeg، png، pdf، doc، docx.',

    // 404 Page
    'page_not_found' => 'الصفحة غير موجودة',
    'page_not_found_message' => 'الصفحة التي تبحث عنها قد تكون أزيلت، أو تغير اسمها، أو أنها غير متوفرة مؤقتًا.',
];
?>
