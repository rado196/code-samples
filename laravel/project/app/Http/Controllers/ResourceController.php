<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResourceController extends Controller
{
  public function sendResource(Request $request, $path)
  {
    if (str_starts_with($path, 'storage/')) {
      $path = storage_path('app/public/' . substr($path, 8));
    } else {
      $path = public_path($path);
    }

    $content = file_get_contents($path);

    return response($content, 200, [
      'Content-Type' => mime_content_type($path),
    ]);
  }
}
