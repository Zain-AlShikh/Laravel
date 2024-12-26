<?php

return [

    'paths' => ['api/*'], // السماح فقط بمسارات API

    'allowed_methods' => ['*'], // السماح بجميع أنواع الطلبات (GET, POST, PUT, DELETE)

    'allowed_origins' => ['*'], // السماح بجميع الأصول (Flutter يستخدم عناوين IP متغيرة)

    'allowed_origins_patterns' => [], // تركه فارغًا إذا كنت تستخدم 'allowed_origins'

    'allowed_headers' => ['*'], // السماح بجميع الرؤوس

    'exposed_headers' => [], // لا حاجة لرؤوس مكشوفة إلا إذا كنت تريد ذلك

    'max_age' => 0, // العمر الأقصى للطلبات

    'supports_credentials' => false, // تعطيل المصادقة عبر الجلسات أو الكوكيز إذا لم تكن ضرورية

];

