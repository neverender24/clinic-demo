<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application ID
    |--------------------------------------------------------------------------
    |
    | This is a unique identifier for your app using reverse-DNS style naming.
    | This Bundle Identifier is critical for app identification across both
    | Android and iOS platforms.
    |
    */

    'app_id' => env('NATIVEPHP_APP_ID', 'com.mobileclinic.app'),

    /*
    |--------------------------------------------------------------------------
    | App Version
    |--------------------------------------------------------------------------
    |
    | Set to "DEBUG" during development to force extraction of bundled code
    | on each boot. In production, cached files are used when versions match.
    |
    */

    'version' => env('NATIVEPHP_APP_VERSION', 'DEBUG'),
    'version_code' => env('NATIVEPHP_APP_VERSION_CODE', '1'),

    /*
    |--------------------------------------------------------------------------
    | Apple Developer Team ID
    |--------------------------------------------------------------------------
    |
    | Your Apple Developer Team ID. Required for building iOS apps.
    |
    */

    'development_team' => env('NATIVEPHP_DEVELOPMENT_TEAM'),

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Enable the permissions your clinic app needs. Each permission must be
    | explicitly enabled. Custom iOS descriptions provide transparency about
    | permission usage.
    |
    */

    'permissions' => [
        // Camera for taking photos of documents, wounds, etc.
        'camera' => true,

        // Biometric authentication for secure login
        'biometric' => true,

        // Location for home visits and patient tracking
        'location' => true,

        // Storage for saving patient documents
        'storage_read' => true,
        'storage_write' => true,

        // Network state detection
        'network_state' => true,

        // Push notifications for appointments and alerts
        'push_notifications' => true,

        // Vibration for haptic feedback
        'vibrate' => true,

        // Disabled by default
        'microphone' => false,
        'microphone_background' => false,
        'nfc' => false,
        'scanner' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | iOS Permission Descriptions
    |--------------------------------------------------------------------------
    |
    | Custom descriptions shown to users when requesting permissions on iOS.
    |
    */

    'ios_descriptions' => [
        'camera' => 'Mobile Clinic needs camera access to capture patient photos and medical documents.',
        'location' => 'Mobile Clinic uses your location for home visit tracking and navigation.',
        'biometric' => 'Mobile Clinic uses Face ID/Touch ID for secure authentication.',
        'push_notifications' => 'Mobile Clinic sends notifications for appointments and important updates.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Device Orientation
    |--------------------------------------------------------------------------
    |
    | Control screen orientation per device type.
    | Options: portrait, upside_down, landscape_left, landscape_right
    |
    */

    'orientation' => [
        'iphone' => ['portrait'],
        'ipad' => ['portrait', 'landscape_left', 'landscape_right'],
        'android' => ['portrait'],
    ],

    /*
    |--------------------------------------------------------------------------
    | iPad Support
    |--------------------------------------------------------------------------
    |
    | Enable iPad support. Note: Once enabled and published to the App Store,
    | iPad support cannot be removed.
    |
    */

    'ipad' => true,

    /*
    |--------------------------------------------------------------------------
    | Cleanup Configuration
    |--------------------------------------------------------------------------
    |
    | Environment keys and files to exclude from the mobile bundle for
    | security and size optimization.
    |
    */

    'cleanup_env_keys' => [
        'DB_PASSWORD',
        'MAIL_PASSWORD',
        'AWS_ACCESS_KEY_ID',
        'AWS_SECRET_ACCESS_KEY',
        'REDIS_PASSWORD',
    ],

    'cleanup_exclude_files' => [
        'node_modules',
        '.git',
        'tests',
        'storage/logs',
        'storage/framework/cache',
        '.env.example',
        'README.md',
        'phpunit.xml',
        'vite.config.js',
    ],

];
