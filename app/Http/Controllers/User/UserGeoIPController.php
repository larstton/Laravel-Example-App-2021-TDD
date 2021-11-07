<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class UserGeoIPController extends Controller
{
    public function __invoke(Request $request)
    {
        $ip = $request->getClientIp();
        if (app()->environment('local')) {
            $ip = '90.196.21.53';
        }

        $result = Cache::remember("geoip-lookup:{$ip}", now()->addMonth(), function () use ($ip) {
            $result = Http::get("http://ip-api.com/json/{$ip}");
            $data = $result->json();
            $status = $data['status'];

            return [
                'result' => $status === 'success' ? $data : null,
                'status' => $status,
                'code'   => $result->status(),
            ];
        });

        if ($result['status'] === 'success') {
            return $this->success([
                'data' => [
                    'region' => data_get($result, 'result.timezone', null),
                ],
            ]);
        }

        $this->errorNotFound();
    }
}
