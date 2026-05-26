<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Services\GeoDistanceService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $shops,
        ]);
    }

    public function store(Request $request)
    {
        $this->assertAdmin($request);

        $shop = Shop::create($this->validateShop($request));

        return response()->json([
            'status' => 'success',
            'message' => 'Da tao co so.',
            'data' => $shop,
        ], 201);
    }

    public function show(Shop $shop)
    {
        return response()->json([
            'status' => 'success',
            'data' => $shop,
        ]);
    }

    public function update(Request $request, Shop $shop)
    {
        $this->assertAdmin($request);

        $shop->update($this->validateShop($request, true));

        return response()->json([
            'status' => 'success',
            'message' => 'Da cap nhat co so.',
            'data' => $shop->refresh(),
        ]);
    }

    public function destroy(Shop $shop)
    {
        $this->assertAdmin(request());

        $shop->update(['is_active' => false]);

        return response()->json([
            'status' => 'success',
            'message' => 'Da tat co so.',
        ]);
    }

    public function deliveryCheck(Request $request, GeoDistanceService $geoDistance)
    {
        $validated = $request->validate([
            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $shop = Shop::query()
            ->where('is_active', true)
            ->findOrFail($validated['shop_id']);

        $distanceKm = $geoDistance->distanceInKm(
            (float) $shop->latitude,
            (float) $shop->longitude,
            (float) $validated['latitude'],
            (float) $validated['longitude']
        );

        $canDeliver = $distanceKm <= (float) $shop->delivery_radius_km;

        return response()->json([
            'status' => 'success',
            'data' => [
                'shop' => $shop,
                'distance_km' => $distanceKm,
                'delivery_radius_km' => (float) $shop->delivery_radius_km,
                'can_deliver' => $canDeliver,
            ],
        ]);
    }

    private function validateShop(Request $request, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'name' => [$required, 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+()\s.-]{8,20}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address_line' => [$required, 'string', 'max:255'],
            'ward' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'latitude' => [$required, 'numeric', 'between:-90,90'],
            'longitude' => [$required, 'numeric', 'between:-180,180'],
            'delivery_radius_km' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function assertAdmin(Request $request): void
    {
        if ($request->user()?->role !== 'admin') {
            abort(403, 'Chi admin moi duoc thuc hien thao tac nay.');
        }
    }
}
