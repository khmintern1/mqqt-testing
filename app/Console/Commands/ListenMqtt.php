<?php
namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class ListenMqtt extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Listen for incoming customer MQTT messages and save to DB';

    public function handle(): void
    {
        $server   = env('MQTT_HOST', '127.0.0.1');
        $port     = (int) env('MQTT_PORT', 1884);
        $username = env('MQTT_AUTH_USERNAME');
        $password = env('MQTT_AUTH_PASSWORD');

        $settings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setKeepAliveInterval(60)
            ->setConnectTimeout(5);

        $listenerId = env('MQTT_CLIENT_ID', 'laravel_app') . '_listener_' . getmypid();
        $mqtt = new MqttClient($server, $port, $listenerId);

        try {
            $mqtt->connect($settings, true);
            $this->info("Connected to MQTT Broker. Listening on 'home/sensors'...");

            $mqtt->subscribe('home/sensors', function (string $topic, string $message) use ($server, $port, $settings) {
                $this->info("Received on [{$topic}]: {$message}");

                try {
                    $data = json_decode($message, true);

                    // Name ပါမပါ စစ်ဆေးပြီး Database ထဲ သိမ်းဆည်းခြင်း
                    if (isset($data['name'])) {
                        $customer = Customer::create([
                            'name'  => $data['name'],
                            'email' => $data['email'] ?? null,
                            'phone' => $data['phone'] ?? null,
                        ]);

                        $this->info("Saved to Database -> Customer ID: {$customer->id}");
                    } else {
                        $this->warn("Payload missing 'name' attribute.");
                    }

                    // MQTTX ဘက်သို့ Success Reply ပြန်ပို့ခြင်း
                    $ackPayload = json_encode([
                        'status' => 'saved to database',
                        'customer_id' => $customer->id ?? null,
                        'time' => now()->toIso8601String(),
                    ]);

                    $replierId = env('MQTT_CLIENT_ID', 'laravel_app') . '_replier_' . rand(100, 999);
                    $publisher = new MqttClient($server, $port, $replierId);
                    $publisher->connect($settings, true);
                    $publisher->publish('home/sensors/response', $ackPayload, 0);
                    $publisher->disconnect();

                    $this->info("Sent acknowledgment reply to [home/sensors/response]");

                } catch (\Exception $e) {
                    $this->error("Error saving to DB or replying: " . $e->getMessage());
                }

            }, 0);

            $mqtt->loop(true);

        } catch (\Exception $e) {
            $this->error("Listener Connection Error: " . $e->getMessage());
        }
    }
}