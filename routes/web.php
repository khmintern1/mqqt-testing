<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    echo env('MQTT_AUTH_USERNAME') . ' - ' . env('MQTT_AUTH_PASSWORD');
    return view('welcome');
});
Route::get('/send-mqtt', function (Request $request) {
    $message = $request->query('msg', 'Hello from Laravel MQTT!');

    $payload = json_encode([
        'message' => $message,
        'sent_at' => now()->toIso8601String(),
    ]);

    // Publish using a unique connection ID so it doesn't kick off your listener
    $mqtt = MQTT::connection('default', 'laravel_publisher_' . uniqid());
    $mqtt->publish('home/sensors', $payload);

    return response()->json([
        'status' => 'Published successfully',
        'sent_payload' => json_decode($payload),
    ]);
});

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

Route::get('/test-mqtt', function () {
    try {
        $server   = env('MQTT_HOST', '127.0.0.1');
        $port     = (int) env('MQTT_PORT', 1884);
        // ID မတူအောင် Random နံပါတ် ထည့်ထားပါသည်
        $clientId = env('MQTT_CLIENT_ID', 'laravel_app') . '_test_' . rand(100, 999);

        $mqtt = new MqttClient($server, $port, $clientId);

        $settings = (new ConnectionSettings)
            ->setUsername(env('MQTT_AUTH_USERNAME'))
            ->setPassword(env('MQTT_AUTH_PASSWORD'))
            ->setConnectTimeout(3);

        $mqtt->connect($settings, true);
        $mqtt->publish('test/topic', 'Hello from Laravel', 0);
        $mqtt->disconnect();

        return 'Connected and Published Successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});