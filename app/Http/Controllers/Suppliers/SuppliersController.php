<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\Supplier\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplierList = Supplier::where('supplier_status',1)->get();
        return view('pages.suppliers.list_suppliers',compact('supplierList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.suppliers.create_suppliers');
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
            'supplier_name' => 'required',
            // 'deligate_phone' => 'required',
            // 'deligate_email' => 'required',
            // 'deligate_transaction_opening_balance' => 'required',
            // 'deligate_licence_file' => 'mimes:pdf,xlx,csv,jpg,png,jpeg|max:2048',
            // 'deligate_picture' => 'mimes:jpg,png,jpeg|max:2048',
        ]);

        $supplier_image = '';

        if($request->hasFile('supplier_image'))
        {
            $supplier_image_file = time().'.'.$request->supplier_image->extension();
            $request->supplier_image->move(public_path('uploads'), $supplier_image_file);
            $supplier_image = 'uploads/'.$supplier_image_file;
        }

        $supplier = new Supplier();
        $supplier->supplier_name = $request->supplier_name;
        $supplier->branch_id = $request->branch_id;
        $supplier->supplier_entry_id = $request->supplier_entry_id;
        $supplier->supplier_email = $request->supplier_email;
        $supplier->supplier_phone_number = $request->supplier_phone_number;
        $supplier->supplier_opening_balance = $request->supplier_opening_balance;
        $supplier->supplier_address = $request->supplier_address;
        $supplier->supplier_created_by =  Auth::user()->id;
        $supplier->supplier_image = $supplier_image;
        $supplier->created_at = date("Y/m/d");
        $supplier->save();
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
        $data['data'] = Supplier::where('supplier_id',$id)->first();
        return view('pages.suppliers.edit_suppliers',$data);
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
        if($request->hasFile('supplier_image'))
        {
            $file_info= Supplier::where('supplier_id',$request->supplier_id)->first();

            if($file_info->supplier_image != null){

                $path = public_path().'/'.$file_info->supplier_image;
                unlink($path);
            }
            $fileName = time().'.'.$request->supplier_image->extension();

            $request->supplier_image->move(public_path('uploads'), $fileName);


            Supplier::where('supplier_id',$request->supplier_id)->update([
                'supplier_image' =>'/uploads/'.$fileName,
            ]);
        }
        Supplier::where('supplier_id',$request->supplier_id)->update([
            'supplier_name' => $request->supplier_name,
            'branch_id' => $request->branch_id,
            'supplier_entry_id' => $request->supplier_entry_id,
            'supplier_email' => $request->supplier_email,
            'supplier_phone_number' => $request->supplier_phone_number,
            'supplier_opening_balance' => $request->supplier_opening_balance,
            'supplier_address' => $request->supplier_address,
            'supplier_updated_by' => Auth::user()->id,
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
        Supplier::where('supplier_id',$id)->update([
            'supplier_deleted_by' => Auth::user()->id,
            'supplier_status' => 0,
            'supplier_is_deleted' => "YES",
        ]);
    }
    public function supplierSearch(Request $request)
    {
        $suppliers = Supplier::where('supplier_name','like',"%{$request->q}%")->orWhere('supplier_entry_id','like',"%{$request->q}%")->orWhere('supplier_phone_number','like',"%{$request->q}%")->get();
        // print_r($clients);
        // die;
        $supplier_array = array();
        foreach ($suppliers as $supplier) {
            $label = $supplier['supplier_name'] . '(' . $supplier['supplier_entry_id'] . ')';
            $value = intval($supplier['supplier_id']);
            $supplier_array[] = array("label" => $label, "value" => $value);
        }
        $result = array('status' => 'ok', 'content' => $supplier_array);
        echo json_encode($result);
        exit;
    }
}
