<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.index');
})->name('home');

Route::get('/about', function () {
    return view('public.about');
})->name('about');

Route::get('/products', function () {
    return view('public.products');
})->name('products');

Route::get('/service', function () {
    return view('public.service');
})->name('service');

Route::get('/event', function () {
    return view('public.event');
})->name('event');

Route::get('/contact', function () {
    return view('public.contact');
})->name('contact');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');