<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingLocationPriceRequest;
use App\Models\ShippingLocationPrice;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use App\Models\Currency;
use Illuminate\Http\Request;

class ShippingLocationPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    public function index()
    {
    // Use client-side DataTables on the index page, so return all rows here
    $datas = ShippingLocationPrice::with(['division', 'district', 'thana'])->latest()->get();
        return view('back.shipping-location.index', compact('datas'));
    }

    public function create()
    {
        $divisions = Division::orderBy('name')->get();
        return view('back.shipping-location.create', compact('divisions'));
    }

    public function store(ShippingLocationPriceRequest $request)
    {
        $input = $request->validated();
        // Convert price from admin currency to base if needed (follow existing ShippingService behavior)
        $curr = Currency::where('is_default',1)->first();
        if ($curr && $curr->value) {
            $input['price'] = (float) ($input['price'] ?? 0) / $curr->value;
        }
        $input['status'] = isset($input['status']) && $input['status'] ? 1 : 0;
        ShippingLocationPrice::create($input);
        return redirect()->route('back.shipping-location.index')->withSuccess(__('Shipping price added.'));
    }

    public function edit(ShippingLocationPrice $shipping_location)
    {
        $divisions = Division::orderBy('name')->get();
        $districts = $shipping_location->division_id ? District::where('division_id', $shipping_location->division_id)->orderBy('name')->get() : collect();
        $thanas = $shipping_location->district_id ? Thana::where('district_id', $shipping_location->district_id)->orderBy('name')->get() : collect();
        return view('back.shipping-location.edit', compact('shipping_location','divisions','districts','thanas'));
    }

    public function update(ShippingLocationPriceRequest $request, ShippingLocationPrice $shipping_location)
    {
        $input = $request->validated();
        $curr = Currency::where('is_default',1)->first();
        if ($curr && $curr->value) {
            $input['price'] = (float) ($input['price'] ?? 0) / $curr->value;
        }
        $input['status'] = isset($input['status']) && $input['status'] ? 1 : 0;
        $shipping_location->update($input);
        return redirect()->route('back.shipping-location.index')->withSuccess(__('Shipping price updated.'));
    }

    public function destroy(ShippingLocationPrice $shipping_location)
    {
        $shipping_location->delete();
        return redirect()->route('back.shipping-location.index')->withSuccess(__('Shipping price deleted.'));
    }

    // Dependent dropdowns
    public function districts(Request $request)
    {
        $divisionId = $request->get('division_id');
        $districts = District::where('division_id', $divisionId)->orderBy('name')->get(['id','name']);
        return response()->json($districts);
    }

    public function thanas(Request $request)
    {
        $districtId = $request->get('district_id');
        $thanas = Thana::where('district_id', $districtId)->orderBy('name')->get(['id','name']);
        return response()->json($thanas);
    }
}
