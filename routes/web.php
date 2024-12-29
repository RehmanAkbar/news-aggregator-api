<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/articles/fetch', function () {
    try {
        
        \App\Jobs\SyncArticlesJob::dispatch(true);
        
        return response()->json([
            'message' => 'Article sync job has been dispatched successfully'
        ]);
    } catch (\Exception $e) {
        $message = "Error dispatching article sync job: {$e->getMessage()}";
        
        return response()->json([
            'message' => $message
        ], 500);
    }
});