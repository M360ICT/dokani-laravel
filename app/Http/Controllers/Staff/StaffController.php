<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffList = Staff::where('staff_status',1)->get();
        return view('pages.staff.list_staff',compact('staffList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.staff.create_staff');
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
            'staff_name' => 'required',
        ]);


        $staff = new Staff();
        $staff->staff_name = $request->staff_name;
        $staff->staff_entry_id = $request->staff_entry_id;
        $staff->staff_created_by =  Auth::user()->id;
        $staff->created_at = date("Y/m/d");
        $staff->save();
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
        $data['data'] = Staff::where('staff_id',$id)->first();
        return view('pages.staff.edit_staff',$data);
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

        Staff::where('staff_id',$request->staff_id)->update([
            'staff_name' => $request->staff_name,
            'staff_entry_id' => $request->staff_entry_id,
            'staff_updated_by' => Auth::user()->id,
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
        Staff::where('staff_id',$id)->update([
            'staff_deleted_by' => Auth::user()->id,
            'staff_status' => 0,
            'staff_is_deleted' => "YES",
        ]);
    }

    public function staffPdf($id)
    {

        $data['data'] = Staff::where('staff_id',$id)->first();
        $pdf = PDF::loadView('pages.staff.pdf', $data);
  
       // return view('pdf');
       return $pdf->download('dokani.pdf');
    }
}