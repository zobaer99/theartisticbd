<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Post;
use App\Models\Transaction;
use App\Repositories\Back\ItemRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\PathaoService;
class BulkDeleteController extends Controller
{
    /**
     * Constructor Method.
     *
     * BulkDeleteController Authentication
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    public function bulkPathaoSend(Request $request, PathaoService $pathaoService)
    {
        $orderIds = $request->ids; // checkbox diye ashbe
        
        // Validate that we have order IDs
        if (empty($orderIds) || !is_array($orderIds)) {
            return back()->withError('No orders selected. Please select at least one order.');
        }
        
        // Remove empty values and get orders
        $orderIds = array_filter($orderIds);
        if (empty($orderIds)) {
            return back()->withError('No valid order IDs found.');
        }
        
        // Convert comma-separated string to array if needed
        if (count($orderIds) == 1 && strpos($orderIds[0], ',') !== false) {
            $orderIds = explode(',', $orderIds[0]);
            $orderIds = array_filter($orderIds);
        }
        
        $orders = Order::whereIn('id', $orderIds)->get();
        
        if ($orders->isEmpty()) {
            return back()->withError('No orders found with the selected IDs.');
        }
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($orders as $order) {
            try {
                // Check if order already has Pathao consignment ID
                if (!empty($order->pathao_consignment_id)) {
                    $errors[] = "Order #{$order->transaction_number} already sent to Pathao (ID: {$order->pathao_consignment_id})";
                    continue;
                }
                
                // Log the order being processed
                Log::info('Sending order to Pathao:', [
                    'order_id' => $order->id,
                    'transaction_number' => $order->transaction_number
                ]);
                
                $response = $pathaoService->createOrder($order);
                
                // Log the response
                Log::info('Pathao order creation response:', [
                    'order_id' => $order->id,
                    'response' => $response
                ]);
                
                // Check different possible response structures
                $consignmentId = null;
                if (isset($response['data']['consignment_id'])) {
                    $consignmentId = $response['data']['consignment_id'];
                } elseif (isset($response['consignment_id'])) {
                    $consignmentId = $response['consignment_id'];
                } elseif (isset($response['data']['id'])) {
                    $consignmentId = $response['data']['id'];
                }
                
                if ($consignmentId) {
                    // Update order with Pathao information
                    $order->pathao_consignment_id = $consignmentId;
                    $order->courier_status = 'sent_to_pathao';
                    $order->save();
                    
                    $successCount++;
                    Log::info('Order successfully sent to Pathao:', [
                        'order_id' => $order->id,
                        'consignment_id' => $consignmentId
                    ]);
                } else {
                    $errorCount++;
                    $errorMsg = "Order #{$order->transaction_number}: " . ($response['message'] ?? 'Unknown error');
                    $errors[] = $errorMsg;
                    
                    Log::error('Failed to send order to Pathao:', [
                        'order_id' => $order->id,
                        'response' => $response
                    ]);
                }
                
            } catch (\Exception $e) {
                $errorCount++;
                $errorMsg = "Order #{$order->transaction_number}: " . $e->getMessage();
                $errors[] = $errorMsg;
                
                Log::error('Exception while sending order to Pathao:', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        // Prepare response message
        $message = "Pathao bulk send completed: {$successCount} successful, {$errorCount} failed.";
        
        if ($successCount > 0 && $errorCount == 0) {
            return back()->withSuccess($message);
        } elseif ($successCount > 0 && $errorCount > 0) {
            $errorDetails = implode('; ', array_slice($errors, 0, 3)); // Show first 3 errors
            if (count($errors) > 3) {
                $errorDetails .= ' and ' . (count($errors) - 3) . ' more errors.';
            }
            return back()->withWarning($message . ' Errors: ' . $errorDetails);
        } else {
            $errorDetails = implode('; ', $errors);
            return back()->withError('Failed to send orders to Pathao. ' . $errorDetails);
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = array_filter($request->ids);
        if (!$ids) {
            return redirect()->back()->withError(__('Selected is empty'));
        }

        $ids = explode(',', $ids[0]);

        if ($request->table == 'items') {
            $ItemRepository = new ItemRepository();
            foreach ($ids as $id) {
                $id = (int)$id;
                $item = Item::findOrFail($id);
                $ItemRepository->delete($item);
            }
        }

        if ($request->table == 'transactions') {
            foreach ($ids as $id) {
                $id = (int)$id;
                Transaction::findOrFail($id)->delete();
            }
        }

        if ($request->table == 'posts') {
            foreach ($ids as $id) {
                $id = (int)$id;
                $post = Post::findOrFail($id);
                $images = json_decode($post->photo, true);
                foreach ($images as $image) {
                    Storage::delete("images" . '/' . $image);
                }
                $post->delete();
            }
        }

        if ($request->table == 'orders') {
            foreach ($ids as $id) {
                $id = (int)$id;
                $order = Order::findOrFail($id);
                $order->tranaction->delete();
                if (Notification::where('order_id', $id)->exists()) {
                    Notification::where('order_id', $id)->delete();
                }
                if (count($order->tracks_data) > 0) {
                    foreach ($order->tracks_data as $track) {
                        $track->delete();
                    }
                }
                $order->delete();
            }
        }

        return redirect()->back()->withSuccess(__('Data Deleted Successfully.'));
    }

    public function getPathaoStatus(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
        $pathaoService = new PathaoService();
        $statuses = [];
        $errorCount = 0;

        foreach ($orderIds as $orderId) {
            try {
                $order = Order::find($orderId);
                if ($order && $order->pathao_consignment_id) {
                    $response = $pathaoService->getOrderInfo($order->pathao_consignment_id);
                    
                    if (isset($response['data']['order_status'])) {
                        $statuses[$orderId] = [
                            'status' => $response['data']['order_status'],
                            'consignment_id' => $order->pathao_consignment_id,
                            'fetched_at' => now()->format('Y-m-d H:i:s')
                        ];
                        
                        Log::info('Pathao status fetched for order:', [
                            'order_id' => $orderId,
                            'consignment_id' => $order->pathao_consignment_id,
                            'status' => $response['data']['order_status']
                        ]);
                    } else {
                        $errorCount++;
                        Log::error('No status data returned for order ' . $orderId);
                    }
                } else {
                    $errorCount++;
                    Log::error('Order ' . $orderId . ' has no Pathao consignment ID');
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Failed to fetch Pathao status for order ' . $orderId . ': ' . $e->getMessage());
            }
        }

        $message = "Fetched Pathao status for " . count($statuses) . " orders";
        if ($errorCount > 0) {
            $message .= ", {$errorCount} failed";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'statuses' => $statuses,
            'errors' => $errorCount
        ]);
    }
}
