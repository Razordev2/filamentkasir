<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/receipt/print', function () {
    if (!session()->has('receipt')) {
        return redirect()->route('pos');
    }
    return view('receipt.print');
})->name('receipt.print');