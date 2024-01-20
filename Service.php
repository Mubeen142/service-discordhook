<?php

namespace App\Services\DiscordHook;

use Illuminate\Support\Facades\Http;
use App\Services\ServiceInterface;
use App\Models\Package;
use App\Models\Order;

class Service implements ServiceInterface
{
    /**
     * Unique key used to store settings 
     * for this service.
     * 
     * @return string
     */
    public static $key = 'discordhook'; 

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    
    /**
     * Returns the meta data about this Server/Service
     *
     * @return object
     */
    public static function metaData(): object
    {
        return (object)
        [
          'display_name' => 'DiscordHook',
          'author' => 'WemX',
          'version' => '1.0.0',
          'wemx_version' => ['dev', '>=1.8.0'],
        ];
    }

    /**
     * Define the default configuration values required to setup this service
     * i.e host, api key, or other values. Use Laravel validation rules for
     *
     * Laravel validation rules: https://laravel.com/docs/10.x/validation
     *
     * @return array
     */
    public static function setConfig(): array
    {
        return [
            [
                "key" => "discordhook::webhook_url",
                "name" => "Webhook URL",
                "description" => "Enter the webhook url to send the message to",
                "type" => "text",
                "rules" => ['required', 'active_url'], // laravel validation rules
            ],
            [
                "key" => "discordhook::username",
                "name" => "Username",
                "description" => "Enter the username of the webhook sender",
                "type" => "text",
                "rules" => ['required'], // laravel validation rules
            ],
        ];
    }

    /**
     * Define the default package configuration values required when creatig
     * new packages. i.e maximum ram usage, allowed databases and backups etc.
     *
     * Laravel validation rules: https://laravel.com/docs/10.x/validation
     *
     * @return array
     */
    public static function setPackageConfig(Package $package): array
    {
        return [
            [
                "key" => "discord_role_id",
                "name" => "Discord Role ID",
                "description" => "Enter the ID of the role to give to the user",
                "type" => "number",
                "rules" => ['required', 'numeric'],
            ],
        ];
    }

    /**
     * Define the checkout config that is required at checkout and is fillable by
     * the client. Its important to properly sanatize all inputted data with rules
     *
     * Laravel validation rules: https://laravel.com/docs/10.x/validation
     *
     * @return array
     */
    public static function setCheckoutConfig(Package $package): array
    {
        return [
            [
                "key" => "discord_user_id",
                "name" => "Discord User ID",
                "description" => "Please enter your discord user ID",
                "type" => "number",
                "rules" => ['required', 'numeric'],
            ],
        ];
    }

    /**
     * Define buttons shown at order management page
     *
     * @return array
     */
    public static function setServiceButtons(Order $order): array
    {
        return [];    
    }

    /**
     * This function is responsible for creating an instance of the
     * service. This can be anything such as a server, vps or any other instance.
     * 
     * @return void
     */
    public function create(array $data = [])
    {
        $order = $this->order;
        $package = $order->package;

        $webhookUrl = settings('discordhook::webhook_url');

        $response = Http::post($webhookUrl, [
            'username' => settings('discordhook::username'),
            'content' => "New order created for {$order->user->username} for package {$package->name}, the user id is {$order->options['discord_user_id']}",
        ]);
    }

    /**
     * This function is responsible for suspending an instance of the
     * service. This method is called when a order is expired or
     * suspended by an admin
     * 
     * @return void
    */
    public function suspend(array $data = [])
    {
        $order = $this->order;
        $package = $order->package;

        $webhookUrl = settings('discordhook::webhook_url');

        $response = Http::post($webhookUrl, [
            'username' => settings('discordhook::username'),
            'content' => "Order has been suspended for {$order->user->username} for package {$package->name}, the user id is {$order->options['discord_user_id']}",
        ]);
    }

    /**
     * This function is responsible for unsuspending an instance of the
     * service. This method is called when a order is activated or
     * unsuspended by an admin
     * 
     * @return void
    */
    public function unsuspend(array $data = [])
    {
        $order = $this->order;
        $package = $order->package;

        $webhookUrl = settings('discordhook::webhook_url');

        $response = Http::post($webhookUrl, [
            'username' => settings('discordhook::username'),
            'content' => "Order has been unsuspended for {$order->user->username} for package {$package->name}, the user id is {$order->options['discord_user_id']}",
        ]);
    }

    /**
     * This function is responsible for deleting an instance of the
     * service. This can be anything such as a server, vps or any other instance.
     * 
     * @return void
    */
    public function terminate(array $data = [])
    {
        $order = $this->order;
        $package = $order->package;

        $webhookUrl = settings('discordhook::webhook_url');

        $response = Http::post($webhookUrl, [
            'username' => settings('discordhook::username'),
            'content' => "Order has been terminated for {$order->user->username} for package {$package->name}, the user id is {$order->options['discord_user_id']}",
        ]);
    }
}
