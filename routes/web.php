<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/getloan/{loan_id}', [InvoiceController::class, 'getLoan']);
Route::get('/getloanpdf/{loan_id}', [InvoiceController::class, 'getLoanPDF']);

Route::get('/getsummary/{loan_id}', [InvoiceController::class, 'getSummaryLoan']);
Route::get('/getsummarypdf/{loan_id}', [InvoiceController::class, 'getSummaryLoanPDF']);
