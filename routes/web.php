<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('invoice.add');
})->name('invoice.add');
Route::get('/', [InvoiceController::class, 'add'])->name('invoice.add');
Route::get('/invoice_list', [InvoiceController::class, 'index'])->name('invoice.list');
Route::get('/invoice_edit/{id}', [InvoiceController::class, 'edit'])->name('invoice.edit');
Route::post('/invoice_update/{id}', [InvoiceController::class, 'update'])->name('invoice.update');
Route::post('/invoice_save', [InvoiceController::class, 'store'])->name('invoice.save');
Route::get('/generate-pdf/{id}', [InvoiceController::class, 'generatePDF'])->name('invoice.pdf');
Route::post('/invoice_delete', [InvoiceController::class, 'destory'])->name('invoice.delete');
Route::post('/custom_validation', [InvoiceController::class, 'custom_validation'])->name('custom_validation');
