<?php

declare(strict_types=1);

use PhpMqtt\Client\MqttClient;

return [

    /*
    |--------------------------------------------------------------------------
    | Default MQTT Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the MQTT connections below you wish
    | to use as your default connection for all MQTT work.
    |
    */

    'default' => env('MQTT_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | MQTT Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the MQTT connections for your application.
    |
    */

    'connections' => [

        'default' => [

            // Host နှင့် Port Configuration
            'host' => env('MQTT_HOST', '127.0.0.1'),
            'port' => (int) env('MQTT_PORT', 1884),

            // MQTT Protocol Version (3.1 သို့မဟုတ် 3.1.1 သို့မဟုတ် 5.0)
            'protocol_version' => MqttClient::MQTT_3_1_1,

            // Client ID Collision မဖြစ်စေရန် uniqid() ထည့်သွင်းထားခြင်း
            'client_id' => env('MQTT_CLIENT_ID', 'laravel_app') . '_' . uniqid(),

            // Connection Settings
            'connection_settings' => [

                // Authentication (Mosquitto Username & Password)
                'auth' => [
                    'username' => env('MQTT_AUTH_USERNAME', null),
                    'password' => env('MQTT_AUTH_PASSWORD', null),
                ],

                // Keep Alive Timeout (Seconds)
                'keep_alive_interval' => (int) env('MQTT_KEEP_ALIVE', 60),

                // Last Will and Testament (LWT) Configuration
                'last_will' => [
                    'topic' => env('MQTT_LAST_WILL_TOPIC', null),
                    'message' => env('MQTT_LAST_WILL_MESSAGE', null),
                    'quality_of_service' => (int) env('MQTT_LAST_WILL_QOS', 0),
                    'retain' => (bool) env('MQTT_LAST_WILL_RETAIN', false),
                ],

                // TLS / SSL Configuration
                'tls' => [
                    'enabled' => (bool) env('MQTT_TLS_ENABLED', false),
                    'allow_self_signed_certificate' => (bool) env('MQTT_TLS_ALLOW_SELF_SIGNED', false),
                    'verify_peer' => (bool) env('MQTT_TLS_VERIFY_PEER', true),
                    'verify_peer_name' => (bool) env('MQTT_TLS_VERIFY_PEER_NAME', true),
                    'ca_file' => env('MQTT_TLS_CA_FILE', null),
                    'ca_path' => env('MQTT_TLS_CA_PATH', null),
                    'client_certificate_file' => env('MQTT_TLS_CLIENT_CERT_FILE', null),
                    'client_certificate_key_file' => env('MQTT_TLS_CLIENT_CERT_KEY_FILE', null),
                    'client_certificate_key_passphrase' => env('MQTT_TLS_CLIENT_CERT_KEY_PASSPHRASE', null),
                ],

                'connect_timeout' => (int) env('MQTT_CONNECT_TIMEOUT', 60),
                'socket_timeout' => (int) env('MQTT_SOCKET_TIMEOUT', 5),
                'resend_timeout' => (int) env('MQTT_RESEND_TIMEOUT', 10),

            ],

        ],

    ],

];