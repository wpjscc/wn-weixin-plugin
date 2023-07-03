<?php 

use Laravel\Sanctum\Sanctum;


return [
    // This contains the Laravel Packages that you want this plugin to utilize listed under their package identifiers
    'packages' => [
        'zoujingli/wechat-developer' => [
            'providers' => [

            ],
            'config_namespace' => 'xcx',
            'config' => [
                'token'          => env('XCX_TOEKN', 'test'),
                'appid'          => env('XCX_APPID',''),
                'appsecret'      => env('XCX_APPSECRET',''),
                'encodingaeskey' => env('XCX_ENCODINGAESKEY',''),
                // 配置商户支付参数（可选，在使用支付功能时需要）
                'mch_id'         => env("XCX_MCH_ID",""),
                'mch_key'        => env("XCX_MCH_KEY",""),
                // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
                'ssl_key'        => env("XCX_SSL_KEY",""),
                'ssl_cer'        => env("XCX_SSL_CER",""),
                // 缓存目录配置（可选，需拥有读写权限）
                'cache_path'     => env("XCX_CACHE_PATH",""),
            ]

        ],
        'zoujingli/wechat-developer-wechat' => [
            'providers' => [

            ],
            'config_namespace' => 'wechat',
            'config' => [
                'token'          => env('WECHAT_TOEKN', 'test'),
                'appid'          => env('WECHAT_APPID',''),
                'appsecret'      => env('WECHAT_APPSECRET',''),
                'encodingaeskey' => env('WECHAT_ENCODINGAESKEY',''),
                // 配置商户支付参数（可选，在使用支付功能时需要）
                'mch_id'         => env("WECHAT_MCH_ID",""),
                'mch_key'        => env("WECHAT_MCH_KEY",""),
                // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
                'ssl_key'        => env("WECHAT_SSL_KEY",""),
                'ssl_cer'        => env("WECHAT_SSL_CER",""),
                // 缓存目录配置（可选，需拥有读写权限）
                'cache_path'     => env("WECHAT_CACHE_PATH",""),
                'redirect' => env('WECHAT_REDIRECT', '')
            ]

        ]

    ],
];
