<?php

namespace App\Http\Controllers\Back;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\{
    Models\Item,
    Models\Attribute,
    Models\AttributeOption,
    Http\Controllers\Controller,
    Http\Requests\AttributeOptionRequest
};
use App\Models\Currency;

class AttributeOptionController extends Controller
{
    /**
     * Constructor Method.
     *
     * Setting Authentication
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Item $item)
    {

        return view('back.item.attribute_option.index',[
            'item'  => $item,
            'curr' => Currency::where('is_default',1)->first(),
            'datas' => $item->join('attributes','attributes.item_id','=','items.id')
                    ->join('attribute_options','attribute_options.attribute_id','=','attributes.id')
                    ->select('attribute_options.id','attribute_options.attribute_id','attribute_options.name','attribute_options.keyword','attribute_options.stock','attribute_options.price','attribute_options.color_image',DB::raw('attributes.name as attribute'))
                    ->where('items.id','=',$item->id)
                    ->latest('id')
                    ->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Item $item)
    {
        $rules = [
            'color_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
        return view('back.item.attribute_option.create',[
            'item'  => $item,
            'curr' => Currency::where('is_default',1)->first(),
            'attributes' => Attribute::whereItemId($item->id)->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeOptionRequest $request, Item $item)
    {

        $input = $request->all();
        $curr = Currency::where('is_default',1)->first();
        $input['price'] = $request->price / $curr->value;
        if ($request->hasFile('color_image')) {
        $imageName = ImageHelper::uploadSummernoteImage($request->file('color_image'), 'images/color_options');
        if ($imageName) {
            $input['color_image'] = $imageName;
        }
}
        AttributeOption::create($input);

        return redirect()->route('back.option.index',$item->id)->withSuccess(__('New Attribute Option Added Successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item, AttributeOption $option)
    {
        $rules = [
            'color_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
        return view('back.item.attribute_option.edit',[
            'item'  => $item,
            'option' => $option,
            'curr' => Currency::where('is_default',1)->first(),
            'attributes' => Attribute::whereItemId($item->id)->get()

        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeOptionRequest $request, Item $item, AttributeOption $option)
    {

        $input = $request->all();
        $curr = Currency::where('is_default',1)->first();
        $input['price'] = $request->price / $curr->value;
        if ($request->hasFile('color_image')) {
        if ($option->color_image) {
            Storage::disk('public')->delete('images/color_options/' . $option->color_image);
        }
        $imageName = ImageHelper::uploadSummernoteImage($request->file('color_image'), 'images/color_options');
        if ($imageName) {
            $input['color_image'] = $imageName;
        }
    }
        $option->update($input);

        return redirect()->route('back.option.index',$item->id)->withSuccess(__('Attribute Option Updated Successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item, AttributeOption $option)
    {
        if ($option->color_image) {
         Storage::disk('public')->delete('images/color_options/' . $option->color_image);
    }
        $option->delete();
        return redirect()->route('back.option.index',$item->id)->withSuccess(__('Attribute Option Deleted Successfully.'));
    }
}
