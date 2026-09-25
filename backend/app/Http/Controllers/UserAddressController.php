<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()
            ->addresses()
            ->latest('is_default')
            ->latest('id')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $addresses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAddress($request);

        $address = DB::transaction(function () use ($request, $validated) {
            $user = $request->user();
            $isFirstAddress = !$user->addresses()->exists();
            $validated['user_id'] = $user->id;
            $validated['is_default'] = (bool) ($validated['is_default'] ?? $isFirstAddress);

            if ($validated['is_default']) {
                $user->addresses()->update(['is_default' => false]);
            }

            return UserAddress::create($validated);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Da them dia chi.',
            'data' => $address,
        ], 201);
    }

    public function show(Request $request, UserAddress $address)
    {
        $this->assertOwnAddress($request, $address);

        return response()->json([
            'status' => 'success',
            'data' => $address,
        ]);
    }

    public function update(Request $request, UserAddress $address)
    {
        $this->assertOwnAddress($request, $address);
        $validated = $this->validateAddress($request, true);

        DB::transaction(function () use ($request, $address, $validated) {
            if ((bool) ($validated['is_default'] ?? false)) {
                $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update($validated);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Da cap nhat dia chi.',
            'data' => $address->refresh(),
        ]);
    }

    public function destroy(Request $request, UserAddress $address)
    {
        $this->assertOwnAddress($request, $address);

        DB::transaction(function () use ($request, $address) {
            $wasDefault = $address->is_default;
            $address->delete();

            if ($wasDefault) {
                $nextDefault = $request->user()
                    ->addresses()
                    ->latest('id')
                    ->first();

                $nextDefault?->update(['is_default' => true]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Da xoa dia chi.',
        ]);
    }

    public function makeDefault(Request $request, UserAddress $address)
    {
        $this->assertOwnAddress($request, $address);

        DB::transaction(function () use ($request, $address) {
            $request->user()->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Da dat dia chi mac dinh.',
            'data' => $address->refresh(),
        ]);
    }

    private function validateAddress(Request $request, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'recipient_name' => [$required, 'string', 'max:255'],
            'phone' => [$required, 'string', 'max:20', 'regex:/^[0-9+()\s.-]{8,20}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address_line' => [$required, 'string', 'max:255'],
            'ward' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'formatted_address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'place_id' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }

    private function assertOwnAddress(Request $request, UserAddress $address): void
    {
        if ((int) $address->user_id !== (int) $request->user()->id) {
            abort(404);
        }
    }
}
