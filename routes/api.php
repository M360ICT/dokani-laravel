<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/* Expense Head */
Route::resource('expense-head', ExpenseHead\ExpenseHeadController::class);

Route::resource('expense-sub-head', ExpenseSubHead\ExpenseSubHeadController::class);

Route::resource('account-transfer', Accounts\AccountTransferController::class);
Route::get('account/balance-statement', 'Accounts\AccountsController@balance_statement');
Route::post('account/save-opening-balance', 'AccountTransactions\AccountTransactionsController@save_opening_balance');
Route::get('search-account', function (Request $request) {
    return search_account($request->q);
});

Route::get('search-account-full-data', function (Request $request) {
    return search_account_full_data($request->q);
});



/* Money Receipt */
Route::resource('money-receipt', MoneyReceipt\MoneyReceiptController::class);
/* Money Receipt */


Route::resource('delivery-men', DeliveryMan\DeliveryManController::class);

/* expense */
Route::resource('expenses', Expenses\ExpensesController::class);

Route::resource('delivery-men', DeliveryMan\DeliveryManController::class);
Route::resource('company-info', CompanyInfo\CompanyInfoController::class);


Route::get('get-invoice-full-information', function (Request $request) {
    return get_invoice_full_information($request->q);
});
