<?php

namespace App\Http\Controllers\AttributeValues;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute\Attribute;
use App\Models\Attribute\AttributeValue;
use Illuminate\Support\Facades\Auth;

class AttributeValuesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['attributesList'] = AttributeValue::where('attributes_value_is_deleted','NO')->get();
        return view('pages.attributes_value.list_attributes_values',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
            $data['attributesList'] = Attribute::all();
        return view('pages.attributes_value.create_attributes_values',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $request->validate([
            'attribute_value_name' => 'required',
        ]);



        $attribute = new AttributeValue();
        $attribute->attributes_id = $request->attribute_id;
        $attribute->attributes_value = $request->attribute_value_name;
        // $color->attributes_value_entry_id = $request->attributes_entry_id;
        $attribute->attributes_value_created_by =  Auth::user()->id;
        $attribute->created_at = date("Y-m-d");
        $attribute->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['attributevalue'] = AttributeValue::where('attributes_value_id',$id)->first();




        $data['attributes'] = Attribute::all();
        return view('pages.attributes_value.edit_attributes_values',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        AttributeValue::where('attributes_value_id',$request->attributes_value_id)->update([
            'attributes_value' => $request->attribute_value_name,
            'attributes_value_entry_id' => $request->attributes_value_entry_id,
            'attributes_value_updated_by' => Auth::user()->id,
            'updated_at' => date("Y/m/d"),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AttributeValue::where('attributes_value_id',$id)->update([
            'attributes_value_deleted_by' => Auth::user()->id,
            
            'attributes_value_is_deleted' => "YES",
        ]);
    }
}