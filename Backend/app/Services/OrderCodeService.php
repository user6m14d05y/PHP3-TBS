<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OrderCodeService
{
    public function generate(): string
    {
        $today = now()->toDateString();

        DB::table('order_sequences')->insertOrIgnore([
            'sequence_date' => $today,
            'current_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = DB::table('order_sequences')
            ->where('sequence_date', $today)
            ->lockForUpdate()
            ->first();

        $nextNumber = ((int) $sequence->current_number) + 1;

        DB::table('order_sequences')
            ->where('sequence_date', $today)
            ->update([
                'current_number' => $nextNumber,
                'updated_at' => now(),
            ]);

        return 'ORD-' . now()->format('Ymd') . '-' . str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
