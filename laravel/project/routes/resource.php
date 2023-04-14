<?php

use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

Route::get('/resource/{url}', [
  ResourceController::class,
  'sendResource',
])->where('url', '.+');
