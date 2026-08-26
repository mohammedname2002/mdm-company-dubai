<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;

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

Route::get('/',[HomeController::class,'index'])->middleware(['auth'])->name('/');


Route::prefix('company/')->name('company.')->middleware('auth')->group(function() {

        Route::get('/create',[CompanyController::class,'create'])->name('create');
        Route::post('/store',[CompanyController::class,'store'])->name('store');
       Route::get('/edit/{id}',[CompanyController::class,'edit'])->name('edit');
        Route::post('/update/{id}',[CompanyController::class,'update'])->name('update');
        Route::get('/',[CompanyController::class,'index'])->name('index');
        Route::post('/delete/{id}',[CompanyController::class,'destroy'])->name('delete');
    });


    Route::prefix('product/')->name('product.')->middleware('auth')->group(function() {

        Route::get('/create',[ProductController::class,'create'])->name('create');
        Route::post('/store',[ProductController::class,'store'])->name('store');
       Route::get('/edit/{id}',[ProductController::class,'edit'])->name('edit');
        Route::post('/update/{id}',[ProductController::class,'update'])->name('update');
        Route::get('/',[ProductController::class,'index'])->name('index');
        Route::post('/delete/{id}',[ProductController::class,'destroy'])->name('delete');
    });
    Route::prefix('invoice/')->name('invoice.')->middleware('auth')->group(function() {

        Route::get('/create',[InvoiceController::class,'create'])->name('create');
        Route::post('/store',[InvoiceController::class,'store'])->name('store');
        Route::get('/edit/{id}',[InvoiceController::class,'edit'])->name('edit');
        Route::post('/update/{id}',[InvoiceController::class,'update'])->name('update');
        Route::get('/',[InvoiceController::class,'index'])->name('index');
        Route::post('/delete/{id}',[InvoiceController::class,'destroy'])->name('delete');
        Route::get('/show/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/print-by-company/{company}', [InvoiceController::class, 'printByCompany'])->name('print-by-company');

    });

    Route::prefix('credit-note/')->name('credit-note.')->middleware('auth')->group(function () {
        Route::get('/', [CreditNoteController::class, 'index'])->name('index');
        Route::get('/create', [CreditNoteController::class, 'create'])->name('create');
        Route::post('/store', [CreditNoteController::class, 'store'])->name('store');
        Route::get('/preview/{invoice}', [CreditNoteController::class, 'preview'])->name('preview');
        Route::get('/show/{id}', [CreditNoteController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [CreditNoteController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [CreditNoteController::class, 'update'])->name('update');
        Route::post('/delete/{id}', [CreditNoteController::class, 'destroy'])->name('delete');
    });
    Route::post('/invoice/download/{id}', [InvoiceController::class, 'downloadInvoice'])->middleware('auth')->name('download');
    Route::post('/credit-note/download/{id}', [CreditNoteController::class, 'downloadCreditNote'])->middleware('auth')->name('credit-note.download');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';