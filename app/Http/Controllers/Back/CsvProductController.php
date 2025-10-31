<?php

namespace App\Http\Controllers\Back;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ChieldCategory;
use App\Models\Item;
use App\Models\Order;
use App\Models\Subcategory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class CsvProductController extends Controller
{

    public function index()
    {
        return view('back.item.bulk-upload');
    }
    public function export()
    {
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=products_csv_export.csv',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $lists = Item::where('item_type', '!=', 'affilite')->get();
        $new_list = [];
        foreach ($lists->toArray() as $list) {
            $list['photo'] = url('/public/storage/images/' . $list['photo']);
            $list['slug'] = Str::random(3) . $list['slug'] . Str::random(2);
            $list['category'] = Category::findOrFail($list['category_id'])->name;
            $list['subcategory'] = $list['subcategory_id'] ? Subcategory::findOrFail($list['subcategory_id'])->name : '';
            $list['childcategory'] = $list['childcategory_id'] ? ChieldCategory::findOrFail($list['childcategory_id'])->name : '';
            $list['brand'] = $list['brand_id'] ? Brand::findOrFail($list['brand_id'])->name : '';
            unset($list['category_id']);
            unset($list['subcategory_id']);
            unset($list['childcategory_id']);
            unset($list['brand_id']);
            $new_list[] = $list;
        }

        # add headers for each column in the CSV download
        array_unshift($new_list, array_keys($new_list[0]));

        $callback = function () use ($new_list) {
            $FH = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Bengali character support
            fprintf($FH, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($new_list as $row) {
                // Ensure all text is properly encoded as UTF-8
                $encoded_row = array_map(function($field) {
                    if (is_string($field)) {
                        return mb_convert_encoding($field, 'UTF-8', 'auto');
                    }
                    return $field;
                }, $row);
                
                fputcsv($FH, $encoded_row);
            }
            fclose($FH);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function transactionExport()
    {
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=transaction_export.csv',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $lists = Transaction::all()->toArray();
        $new_list = [];
        foreach ($lists as $list) {
            $new_list[] = $list;
        }

        # add headers for each column in the CSV download
        array_unshift($new_list, array_keys($new_list[0]));

        $callback = function () use ($new_list) {
            $FH = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Bengali character support
            fprintf($FH, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($new_list as $row) {
                // Ensure all text is properly encoded as UTF-8
                $encoded_row = array_map(function($field) {
                    if (is_string($field)) {
                        return mb_convert_encoding($field, 'UTF-8', 'auto');
                    }
                    return $field;
                }, $row);
                
                fputcsv($FH, $encoded_row);
            }
            fclose($FH);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function orderExport()
    {
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=order_csv_export.csv',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $orders = Order::all();
        $new_list = [];
        
        foreach ($orders as $order) {
            // Parse cart data
            $cart = json_decode($order->cart, true);
            
            // Parse shipping info
            $shipping_info = json_decode($order->shipping_info, true);
            
            // Parse shipping details
            $shipping_data = json_decode($order->shipping, true);
            
            // Extract each product from the cart
            foreach ($cart as $cart_key => $item) {
                $row = [
                    'order_id' => $order->id,
                    'order_date' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '',
                    'order_status' => $order->order_status,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'transaction_number' => $order->transaction_number,
                    
                    // Product details
                    'product_name' => $item['name'] ?? '',
                    // 'product_slug' => $item['slug'] ?? '',
                    'quantity' => $item['qty'] ?? 1,
                    'product_price' => $item['main_price'] ?? 0,
                    'attribute_price' => $item['attribute_price'] ?? 0,
                    'total_product_price' => ($item['main_price'] ?? 0) + ($item['attribute_price'] ?? 0),
                    
                    // Product attributes (size, color, etc.)
                    'color' => '',
                    'size' => '',
                    'other_attributes' => '',
                    
                    // Customer info from shipping
                    'customer_name' => ($shipping_info['ship_first_name'] ?? '') . ' ' . ($shipping_info['ship_last_name'] ?? ''),
                    // 'customer_email' => $shipping_info['ship_email'] ?? '',
                    'customer_phone' => $shipping_info['ship_phone'] ?? '',
                    'customer_address' => $shipping_info['ship_address1'] ?? '',
                    'customer_city' => $shipping_info['ship_city'] ?? '',
                    'customer_zip' => $shipping_info['ship_zip'] ?? '',
                    // 'customer_country' => $shipping_info['ship_country'] ?? '',
                    
                    // Pricing
                    'shipping_cost' => $shipping_data['price'] ?? 0,
                    'shipping_method' => $shipping_data['title'] ?? '',
                    'discount_amount' => 0, // Will calculate if discount exists
                    // 'state_price' => $order->state_price ?? 0,
                    'total_order_amount' => 0, // Will calculate
                ];
                
                // Extract attributes (color, size, etc.)
                if (isset($item['attribute']) && is_array($item['attribute'])) {
                    $attributes = [];
                    $color = '';
                    $size = '';
                    
                    if (isset($item['attribute']['names']) && isset($item['attribute']['option_name'])) {
                        foreach ($item['attribute']['names'] as $key => $attr_name) {
                            $attr_value = $item['attribute']['option_name'][$key] ?? '';
                            
                            // Identify common attributes
                            if (strtolower($attr_name) === 'color') {
                                $color = $attr_value;
                            } elseif (strtolower($attr_name) === 'size') {
                                $size = $attr_value;
                            } else {
                                $attributes[] = $attr_name . ': ' . $attr_value;
                            }
                        }
                    }
                    
                    $row['color'] = $color;
                    $row['size'] = $size;
                    $row['other_attributes'] = implode(', ', $attributes);
                }
                
                // Calculate discount if exists
                $discount = json_decode($order->discount, true);
                if (is_array($discount) && !empty($discount)) {
                    // Assuming discount is an array with discount amount
                    $row['discount_amount'] = array_sum($discount);
                }
                
                // Calculate total order amount
                $subtotal = ($row['total_product_price'] * $row['quantity']);
                $row['total_order_amount'] = $subtotal + $row['shipping_cost'] - $row['discount_amount'];
                
                $new_list[] = $row;
            }
        }

        // Add headers for each column in the CSV download
        if (!empty($new_list)) {
            array_unshift($new_list, array_keys($new_list[0]));
        }

        $callback = function () use ($new_list) {
            $FH = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Bengali character support
            fprintf($FH, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($new_list as $row) {
                // Ensure all text is properly encoded as UTF-8
                $encoded_row = array_map(function($field) {
                    if (is_string($field)) {
                        // Convert to UTF-8 and handle Bengali characters properly
                        return mb_convert_encoding($field, 'UTF-8', 'auto');
                    }
                    return $field;
                }, $row);
                
                fputcsv($FH, $encoded_row);
            }
            fclose($FH);
        };
        return response()->stream($callback, 200, $headers);
    }

    //*** POST Request
    public function import(Request $request)
    {

        try {
            $filename = '';
            if ($file = $request->file('csv')) {
                $filename = time() . "." . $file->getClientOriginalExtension();
                $file->move('assets/temp_files', $filename);
            }

            $file = fopen('assets/temp_files/' . $filename, "r");

            $i = 1;

            while (($line = fgetcsv($file)) !== false) {

                if ($i != 1) {

                    $category_id = $line[31] ? (Category::whereName($line[31])->exists() ? Category::whereName($line[31])->first()->id : 0) : 0;
                    $subcategory_id = $line[32] ? (SubCategory::whereName($line[32])->exists() ? SubCategory::whereName($line[32])->first()->id : 0) : 0;
                    $childcategory_id = $line[33] ? (ChieldCategory::whereName($line[33])->exists() ? ChieldCategory::whereName($line[33])->first()->id : 0) : 0;
                    $brand_id = $line[34] ? (Brand::whereName($line[33])->exists() ? Brand::whereName($line[33])->first()->id : 0) : 0;

                    $input['category_id'] = $category_id;
                    $input['subcategory_id'] = $subcategory_id;
                    $input['childcategory_id'] = $childcategory_id;
                    $input['brand_id'] = $brand_id;
                    $input['tax_id'] = $line[4];
                    $input['name'] = $line[6];
                    $input['slug'] = $line[7];
                    $input['sku'] = $line[8];
                    $input['tags'] = $line[9];
                    $input['video'] = $line[10];
                    $input['sort_details'] = $line[11];
                    $input['specification_name'] = $line[12];
                    $input['specification_description'] = $line[13];
                    $input['is_specification'] = $line[14];
                    $input['details'] = $line[15];
                    $input['discount_price'] = $line[17];
                    $input['previous_price'] = $line[18];
                    $input['stock'] = $line[19];
                    $input['meta_keywords'] = $line[20];
                    $input['meta_description'] = $line[21];
                    $input['status'] = $line[22];
                    $input['is_type'] = $line[23];
                    $input['date'] = $line[24];
                    $input['file'] = $line[26];
                    $input['link'] = $line[27];
                    $input['file_type'] = $line[28] ? $line[28] : null;

                    $input['item_type'] = $line[25];

                    $images_name = $this->uploadImage($line[16], 'images');
                    

                    $input['photo'] = $images_name[1];
                    $input['thumbnail'] = $images_name[1];



                    $data = new Item();
                    $data->fill($input)->save();
                }

                $i++;
            }
            fclose($file);

            $removefiles = glob('/assets/temp_files/*');

            // Deleting all the files in the list
            foreach ($removefiles as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            return back()->withSuccess(__('Bulk Product File Imported Successfully.'));
        } catch (\Throwable $th) {
            dd($th);
            return back()->withError(__('Something is wrong!'));
        }
    }


    public function uploadImage($file, $path, $delete = null)
    {
        if ($file) {

            if ($delete) {
                Storage::delete($path . '/' . $delete);
            }

            $photoName = 'OM_' . time() .  Str::random(8) . '.png';
            $thumbnailName = 'OM_' . time() .  Str::random(8) . '.png';

            Storage::putFileAs($path, $file, $photoName);


            $image = \Image::make($file)->resize(230, 230);


            $thumbnailPath = $path . '/' . $thumbnailName;
            Storage::put($thumbnailPath, (string) $image->encode());


            return [$photoName, $thumbnailName];
        }
    }
}
