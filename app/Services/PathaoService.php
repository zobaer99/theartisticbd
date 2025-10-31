<?php
/**
 * @Author: Anwarul
 * @Date: 2025-08-27 16:30:32
 * @LastEditors: Anwarul
 *        
        // Log the response for debugging
        Log::info('Pathao price calculation response:', [
            'request' => $requestData,
            'response' => $result,
            'status' => $response->status()
        ]);e: 2025-08-27 18:27:31
 * @Description: Innova IT
 */

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PathaoService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = env('PATHAO_BASE_URL', 'https://api-hermes.pathao.com');
        $this->authenticate();
    }

    private function authenticate()
    {
        $response = Http::post($this->baseUrl . '/aladdin/api/v1/issue-token', [
            'client_id' => env('PATHAO_CLIENT_ID'),
            'client_secret' => env('PATHAO_CLIENT_SECRET'),
            'username' => env('PATHAO_USERNAME'),
            'password' => env('PATHAO_PASSWORD'),
            'grant_type' => 'password'
        ]);

        $data = $response->json();
        
        if ($response->status() !== 200) {
            Log::error('Pathao authentication failed:', [
                'status' => $response->status(),
                'response' => $data,
                'base_url' => $this->baseUrl
            ]);
        }
        
        $this->token = $data['access_token'] ?? null;
        
        if (!$this->token) {
            Log::error('Pathao authentication failed - no access token received');
        }
    }

    public function getCities()
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . '/aladdin/api/v1/city-list')
            ->json();
    }

    public function getZones($cityId)
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . "/aladdin/api/v1/cities/{$cityId}/zone-list")
            ->json();
    }

    public function getAreas($zoneId)
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . "/aladdin/api/v1/zones/{$zoneId}/area-list")
            ->json();
    }

    public function calculatePrice($cityId, $zoneId, $itemWeight = 0.5, $itemType = 2, $deliveryType = 48)
    {
        $response = Http::withToken($this->token)
            ->post($this->baseUrl . '/aladdin/api/v1/merchant/price-plan', [
                'store_id' => 186927, // Your store ID
                'item_type' => (int)$itemType,
                'delivery_type' => (int)$deliveryType,
                'item_weight' => (float)$itemWeight,
                'recipient_city' => (int)$cityId,
                'recipient_zone' => (int)$zoneId
            ]);
            
        $result = $response->json();
        
        // Log the response for debugging
        \Log::info('Pathao price calculation response:', [
            'request' => [
                'store_id' => 186927,
                'item_type' => (int)$itemType,
                'delivery_type' => (int)$deliveryType,
                'item_weight' => (float)$itemWeight,
                'recipient_city' => (int)$cityId,
                'recipient_zone' => (int)$zoneId
            ],
            'response' => $result,
            'status' => $response->status()
        ]);
        
        return $result;
    }

    public function createOrder($order)
    {
        // Ensure we have a valid token
        if (!$this->token) {
            Log::error('Pathao createOrder failed: No authentication token available');
            return [
                'message' => 'Authentication failed',
                'type' => 'error',
                'code' => 401
            ];
        }
        
        $shipping = json_decode($order->shipping_info, true);
        
        // Get shipping name
        $shipName = '';
        if(!empty($shipping)){
            foreach(['ship_first_name','ship_name','name','ship_full_name'] as $key){
                if(!empty($shipping[$key])){ 
                    $shipName = $shipping[$key]; 
                    break; 
                }
            }
        }
        
        // If no shipping name found, try user name
        if(empty($shipName) && $order->user) {
            $shipName = trim($order->user->first_name . ' ' . $order->user->last_name);
        }
        
        // Fallback to guest user
        if(empty($shipName)) {
            $shipName = 'Guest User';
        }
        
        // Calculate total quantity
        $totalQty = 1; // default
        if($order->cart) {
            $cart = json_decode($order->cart, true);
            $totalQty = array_sum(array_column($cart, 'qty'));
        }
        
        // Get Pathao IDs from shipping info (saved from form)
        $recipientCity = $shipping['pathao_city_id'] ?? 1;
        $recipientZone = $shipping['pathao_zone_id'] ?? 12;
        $recipientArea = $shipping['pathao_area_id'] ?? 333;
        
        // Get and validate recipient address
        $recipientAddress = $shipping['ship_address1'] ?? $shipping['ship_address'] ?? $shipping['address'] ?? '';
        
        // Ensure address is at least 10 characters (Pathao requirement)
        if (strlen($recipientAddress) < 10) {
            if (empty($recipientAddress)) {
                $recipientAddress = 'Customer Address, Bangladesh';
            } else {
                $recipientAddress = $recipientAddress . ', Bangladesh';
            }
        }
        
        // Get recipient phone and validate
        $recipientPhone = $shipping['ship_phone'] ?? $shipping['phone'] ?? 'N/A';
        
        // Calculate total amount to collect (order total, not just shipping)
        $orderTotal = \App\Helpers\PriceHelper::OrderTotal($order);
        
        // Prepare item description from order
        $itemDescription = 'Order items';
        if($order->cart) {
            $cart = json_decode($order->cart, true);
            $itemNames = array_column($cart, 'name');
            if (!empty($itemNames)) {
                $itemDescription = 'Items: ' . implode(', ', array_slice($itemNames, 0, 3));
                if (count($itemNames) > 3) {
                    $itemDescription .= ' and ' . (count($itemNames) - 3) . ' more items';
                }
            }
        }
        
        $requestData = [
            "store_id" => 186927, // Pathao registered store ID
            "merchant_order_id" => (string)$order->id,
            "recipient_name" => $shipName,
            "recipient_phone" => $recipientPhone,
            "recipient_address" => $recipientAddress,
            "recipient_city" => (int)$recipientCity,
            "recipient_zone" => (int)$recipientZone,
            "recipient_area" => (int)$recipientArea,
            "delivery_type" => 48,
            "item_type" => 2,
            "special_instruction" => "Handle with care",
            "item_quantity" => $totalQty,
            "item_weight" => "0.5", // String format as per API doc
            "item_description" => $itemDescription,
            "amount_to_collect" => (float)$orderTotal,
        ];
        
        // Log the request for debugging
        Log::info('Pathao order creation request:', [
            'order_id' => $order->id,
            'request_data' => $requestData
        ]);
        
        $response = Http::withToken($this->token)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post($this->baseUrl . '/aladdin/api/v1/orders', $requestData);
            
        $result = $response->json();
        
        // Log the response for debugging
        Log::info('Pathao order creation response:', [
            'order_id' => $order->id,
            'response' => $result,
            'status' => $response->status()
        ]);
        
        return $result;
    }
    
    public function getOrderInfo($consignmentId)
    {
        // Ensure we have a valid token
        if (!$this->token) {
            Log::error('Pathao getOrderInfo failed: No authentication token available');
            return [
                'message' => 'Authentication failed',
                'type' => 'error',
                'code' => 401
            ];
        }
        
        $response = Http::withToken($this->token)
            ->get($this->baseUrl . "/aladdin/api/v1/orders/{$consignmentId}/info");
            
        $result = $response->json();
        
        // Log the response for debugging
        Log::info('Pathao order info response:', [
            'consignment_id' => $consignmentId,
            'response' => $result,
            'status' => $response->status()
        ]);
        
        return $result;
    }
    
    public function getCityName($cityId)
    {
        $cities = $this->getCities();
        if (isset($cities['data']['data'])) {
            foreach ($cities['data']['data'] as $city) {
                if ($city['city_id'] == $cityId) {
                    return $city['city_name'];
                }
            }
        }
        return 'Unknown City';
    }
    
    public function getZoneName($cityId, $zoneId)
    {
        $zones = $this->getZones($cityId);
        if (isset($zones['data']['data'])) {
            foreach ($zones['data']['data'] as $zone) {
                if ($zone['zone_id'] == $zoneId) {
                    return $zone['zone_name'];
                }
            }
        }
        return 'Unknown Zone';
    }
    
    public function getAreaName($zoneId, $areaId)
    {
        $areas = $this->getAreas($zoneId);
        if (isset($areas['data']['data'])) {
            foreach ($areas['data']['data'] as $area) {
                if ($area['area_id'] == $areaId) {
                    return $area['area_name'];
                }
            }
        }
        return 'Unknown Area';
    }
}
