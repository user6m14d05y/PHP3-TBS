<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeocodeController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('q');

        if (!$query) {
            return response()->json([], 400);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'TBS-Flower-Shop/1.0 (PHP3 student project Backend)',
            ])->get('https://nominatim.openstreetmap.org/search', [
                'format' => 'json',
                'q' => $query,
                'limit' => 5,
                'addressdetails' => 1
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi kết nối dịch vụ tìm tọa độ.'
            ], 500);
        }
    }
}
