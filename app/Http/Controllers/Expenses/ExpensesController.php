<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountTransactionsResource;
use App\Http\Resources\ExpenseResource;
use App\Models\AccountTransaction\AccountTransaction;
use App\Models\Expense\Expense;
use App\Models\ExpenseHead\ExpenseHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpensesController extends Controller
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
            $books = Expense::paginate();
            return ExpenseResource::collection($books);
        } else {
            $data['expense'] = Expense::latest()->get();
            return view('pages.expenses.list_expenses', $data);
        }
         return view('pages.expenses.list_expenses');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['head'] = ExpenseHead::where('status',1)->get();
        return view('pages.expenses.create_expenses',$data);
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
                'expense_head_id' => 'required|integer',
                'expense_sub_head_id' => 'required|integer',
                'expense_account' => 'required|integer',
                'created_by' => 'required|integer',
            ];

  




            $validator = Validator::make($request->all(), $data);
            

            if ($validator->fails()) {
                return ['errors' => $validator->errors()->first()];
            } else {

                $validated = $validator->validated();
                $validated['status'] = '1';
                $validated['expense_date'] = date('Y-m-d');
                $statement = Expense::create($validated);
                $transaction['transaction_type'] = $request->transaction_type;
                $transaction['transaction_account_id'] = $validated['expense_id'];
                $transaction['transaction_amount'] = $request->transaction_amount;
                $transactionStatement = AccountTransaction::create($transaction);
                return new AccountTransactionsResource($transactionStatement);
                return new ExpenseResource($statement);
            }
        } else {

            //   print_r($request->all());die;
            // $data = [
            //     'expense_head_id' => 'required|integer',
            //     'expense_sub_head_id' => 'required|integer',
            //     'expense_account' => 'required|integer',
            // ];

            // $validator = Validator::make($request->all(), $data);

            // if ($validator->fails()) {
            //     return ['errors' => $validator->errors()->first()];
            // } else {

            //     $validated = $validator->validated();
            //     $validated['created_by'] = Auth::user()->id;
            //     $statement = Expense::create($validated);
            //     return ['status' => 'okay'];
            // }

            $data = [
                'expense_head_id' => 'required|integer',
                'expense_sub_head_id' => 'required|integer',
                'expense_account' => 'required|integer',
                'expense_amount'  => 'required|integer'
            ];






            $validator = Validator::make($request->all(), $data);


            if ($validator->fails()) {
                return ['errors' => $validator->errors()->first()];
            } else {

                $validated = $validator->validated();
                $validated['status'] = '1';
                $statement = Expense::create($validated);
                $transaction['transaction_type'] = $request->transaction_type;
                $transaction['transaction_account_id'] = $statement->expense_account;
                $transaction['transaction_amount'] = $request->expense_amount;
                $transaction['transaction_type'] = 'DEBIT';
                $transaction['transaction_note'] = $request->expense_note;
                $transaction['transaction_date'] = date('Y-m-d');
                $transaction['transaction_method'] = 'BANK';
                $transaction['transaction_for'] = 'EXPENSE';
                $transactionStatement = AccountTransaction::create($transaction);

                $account_current_balance = get_acoount_current_balance_by_account_id($request->expense_account);
                $update_client_transection = AccountTransaction::find($transactionStatement->transaction_id)->update([
                    'transaction_last_balance' => $account_current_balance
                ]);

                // return new AccountTransactionsResource($transactionStatement);
                // return new ExpenseResource($statement);



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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}