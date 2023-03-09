<?php

namespace App\Http\Controllers\Attribute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute\Attribute;
use Illuminate\Support\Facades\Auth;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $data['attributesList'] = Attribute::where('attributes_is_deleted','NO')->get();
        return view('pages.attributes.list_attributes',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        $data['attributesList'] = Attribute::all();
        return view('pages.attributes.create_attributes',$data);
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
            'attributes_name' => 'required',
        ]);


        $color = new Attribute();
        $color->attributes_name = $request->attributes_name;
        $color->attributes_entry_id = $request->attributes_entry_id;
        $color->attributes_created_by =  Auth::user()->id;
        $color->created_at = date("Y-m-d");
        $color->save();
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
        $data['data'] = Attribute::where('attributes_id',$id)->first();
        return view('pages.attributes.edit_attributes',$data);
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
        Attribute::where('attributes_id',$request->attributes_id)->update([
            'attributes_name' => $request->attributes_name,
            'attributes_entry_id' => $request->attributes_entry_id,
            'attributes_updated_by' => Auth::user()->id,
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
        Attribute::where('attributes_id',$id)->update([
            'attributes_deleted_by' => Auth::user()->id,
            
            'attributes_is_deleted' => "YES",
        ]);
    }
}