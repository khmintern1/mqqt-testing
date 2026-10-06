
<?php
use Illuminate\Support\Facades\Route;
use PhpMqtt\Client\Facades\MQTT;

Route::get('/send-mqtt', function () {
    $payload = json_encode([
        'temperature' => rand(20, 30),
        'humidity' => rand(40, 60),
        'timestamp' => now()->toIso8601String(),
    ]);

    MQTT::publish('home/sensors', $payload);

    return response()->json([
        'status' => 'Message published successfully!',
        'payload' => json_decode($payload),
    ]);
});