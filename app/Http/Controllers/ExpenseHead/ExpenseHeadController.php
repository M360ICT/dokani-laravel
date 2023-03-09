<?php

namespace App\Http\Controllers\ExpenseHead;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpenseHeadResource;
use App\Models\ExpenseHead\ExpenseHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpenseHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $is_api_request = Request()->route()->getPrefix() === 'api';


        if ($is_api_request) {
            $books = ExpenseHead::paginate();
            return ExpenseHeadResource::collection($books);
        }else{
             $data['head'] = ExpenseHead::where('status',1)->get();
             return view('pages.expensehead.list_expense_head', $data);
        }




        // $data['head'] = ExpenseHead::where('status',1)->get();
        // return view('pages.expensehead.list_expense_head',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.expensehead.create_expense_head');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $is_api_request = $request->route()->getPrefix() === 'api';
        if ($is_api_request) {
           //data get    
            $data = [
                'title' => 'required|string|max:255',
                'created_by' => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $data);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()->first()];
            } else {

                $validated = $validator->validated();
                $validated['status'] = '1';
                $statement = ExpenseHead::create($validated);
                return new ExpenseHeadResource($statement);
            }

        }else{
            $data = [
                'title' => 'required|string|max:255',
                //'created_by' => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $data);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()->first()];
            } else {

                $validated = $validator->validated();
                $validated['created_by'] = Auth::user()->id;
                $validated['status'] = '1';
                $statement = ExpenseHead::create($validated);
                return ['status' => 'okay'];
            }
        }


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
        $data['head'] = ExpenseHead::where('expensehead_id', $id)->first();
        return view('pages.expensehead.edit_expense_head', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpenseHead $expense_head)
    {

        $is_api_request = $request->route()->getPrefix() === 'api';
        if ($is_api_request) {
            //data get    
            $data = [
                'title' => 'required|string|max:255',
                'created_by' => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $data);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()->first()];
            } else {

                $validated = $validator->validated();
                $validated['status'] = '1';
                $expense_head->update($validated);


                
                //$statement = ExpenseHead::create($validated);
                return new ExpenseHeadResource($expense_head);
            }
        } else {
            $head = ExpenseHead::where('expensehead_id', $request->expense_head_id)->update([
                'title' => $request->expense_head_name,
                'updated_by' => Auth::user()->id
            ]);

        }
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


            $expense_head = ExpenseHead::find($id);
            $expense_head->status = 0;
            $expense_head->save();
        

    }
}