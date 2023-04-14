<?php

namespace App\Http\Middleware;

use App\Models\PaymentLog;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class PaymentLogger
{
  private function parseHeaders(array &$headers)
  {
    foreach ($headers as $headerKey => $headerValueArray) {
      $headers[$headerKey] = $headerValueArray[0];
    }

    return $headers;
  }

  private function getUserId()
  {
    try {
      if (Auth::check()) {
        return Auth::id();
      }

      return null;
    } catch (Throwable $e) {
      return null;
    }
  }

  private function createLog(Request $request)
  {
    $headers = $request->headers->all();
    $headers = $this->parseHeaders($headers);

    $paymentLog = new PaymentLog();
    $paymentLog->user_id = $this->getUserId();
    $paymentLog->url = $request->url();
    $paymentLog->http_method = $request->method();
    $paymentLog->request_body = $request->all();
    $paymentLog->request_headers = $headers;
    $paymentLog->save();

    return $paymentLog;
  }

  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    if (strpos($request->url(), 'history') !== false) {
      return $next($request);
    }

    $log = $this->createLog($request);
    $request->merge([
      'paymentLog' => $log,
    ]);

    try {
      /** @var JsonResponse */
      $response = $next($request);

      $headers = $response->headers->all();
      $headers = $this->parseHeaders($headers);

      $log->status_code = $response->getStatusCode();
      $log->response_headers = $headers;

      if ($request instanceof JsonResponse) {
        $log->response_body = json_decode($response->content());
      } else {
        $log->response_body = $response->content();
      }
    } catch (Throwable $e) {
      $log->status_code = 500;
      $log->response_headers = [];
      $log->response_data = [
        'error' => [
          'message' => $e->getMessage(),
          'stack' => $e->getTraceAsString(),
        ],
      ];

      $response = response()->json(['status' => 'failure'], 500);
    }

    $log->save();

    return $response;
  }
}
