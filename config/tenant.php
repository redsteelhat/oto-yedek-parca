<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Tenant Slug
    |--------------------------------------------------------------------------
    |
    | Development ortamında veya subdomain kullanmayan kurulumlarda hangi
    | tenant'ın kullanılacağını belirlemek için kullanılabilir. ENV üzerinden
    | DEFAULT_TENANT değeri ile doldurulur.
    |
    */

'default_slug' => env('DEFAULT_TENANT', null),

    /*
    |--------------------------------------------------------------------------
    | Automatic Fallback
    |--------------------------------------------------------------------------
    |
    | Eğer root domaine gelen isteklerde otomatik olarak bir tenant'a düşmek
    | isterseniz bu değeri true yapabilirsiniz. Varsayılan olarak false bırakıp
    | çoklu tenant ana sayfasının gösterilmesini sağlayabilirsiniz.
    |
    */

    'auto_fallback' => env('TENANT_AUTO_FALLBACK', false),

    /*
    |--------------------------------------------------------------------------
    | Fallback to First Active Tenant
    |--------------------------------------------------------------------------
    |
    | Eğer default slug bulunamazsa veya tanımlı değilse aktif durumdaki ilk
    | tenant'ın otomatik olarak seçilmesini isterseniz true bırakın.
    |
    */

    'fallback_to_first_active' => env('TENANT_FALLBACK_FIRST_ACTIVE', false),
];


