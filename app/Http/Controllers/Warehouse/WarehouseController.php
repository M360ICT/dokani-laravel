<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $warehouseList = Warehouse::where('warehouse_status',1)->get();
        return view('pages.warehouse.list_warehouse',compact('warehouseList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.warehouse.create_warehouse');
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
            'warehouse_name' => 'required|unique:warehouses,warehouse_name',
            // 'deligate_phone' => 'required',
            // 'deligate_email' => 'required',
            // 'deligate_transaction_opening_balance' => 'required',
            // 'deligate_licence_file' => 'mimes:pdf,xlx,csv,jpg,png,jpeg|max:2048',
            // 'deligate_picture' => 'mimes:jpg,png,jpeg|max:2048',
        ]);


        $warehouse = new Warehouse();
        $warehouse->warehouse_name = $request->warehouse_name;
        $warehouse->warehouse_entry_id = $request->warehouse_entry_id;
        $warehouse->warehouse_phone_number = $request->warehouse_phone_number;
        $warehouse->warehouse_address = $request->warehouse_address;
        $warehouse->warehouse_created_by =  Auth::user()->id;
        $warehouse->created_at = date("Y/m/d");
        $warehouse->save();
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
        $data['data'] = Warehouse::where('warehouse_id',$id)->first();
        return view('pages.warehouse.edit_warehouse',$data);
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

        Warehouse::where('warehouse_id',$request->warehouse_id)->update([
            'warehouse_name' => $request->warehouse_name,
            'warehouse_entry_id' => $request->warehouse_entry_id,
            'warehouse_phone_number' => $request->warehouse_phone_number,
            'warehouse_address' => $request->warehouse_address,
            'warehouse_updated_by' => Auth::user()->id,
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
        Warehouse::where('warehouse_id',$id)->update([
            'warehouse_deleted_by' => Auth::user()->id,
            'warehouse_status' => 0,
            'warehouse_is_deleted' => "YES",
        ]);
    }

    public function warehouseSearch(Request $request)
    {
        $warehouses = Warehouse::where('warehouse_name','like',"%{$request->q}%")->orWhere('warehouse_entry_id','like',"%{$request->q}%")->orWhere('warehouse_phone_number','like',"%{$request->q}%")->get();
        // print_r($clients);
        // die;
        $warehouse_array = array();
        foreach ($warehouses as $warehouse) {
            $label = $warehouse['warehouse_name'] . '(' . $warehouse['warehouse_entry_id'] . ')';
            $value = intval($warehouse['warehouse_id']);
            $warehouse_array[] = array("label" => $label, "value" => $value);
        }
        $result = array('status' => 'ok', 'content' => $warehouse_array);
        echo json_encode($result);
        exit;
    }
}
