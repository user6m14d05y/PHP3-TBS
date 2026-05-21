<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:1000'],
        ]);

        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Chatbot chưa được cấu hình API key.',
            ], 500);
        }

        $prompt = "Bạn là chatbot tư vấn của TBS Flora, một cửa hàng hoa tươi tại Việt Nam. Hãy trả lời bằng tiếng Việt, cực kỳ ngắn gọn, thân thiện và cô đọng (tối đa 2-3 câu hoặc 1 đoạn văn nhỏ). Không viết dài dòng lê thê. Nếu khách hỏi tư vấn mẫu hoa hoặc ngân sách, chỉ trả lời 1-2 câu giới thiệu ngắn gọn vì hệ thống sẽ tự động hiển thị các thẻ sản phẩm thực tế kèm hình ảnh và giá bán ngay bên dưới cho khách hàng tự chọn. Tránh dùng danh sách liệt kê hoa bằng ký tự quá dài dòng.\n\nKhách hàng: {$validated['message']}";

        $response = Http::timeout(20)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key={$apiKey}",
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 500,
                ],
            ]
        );

        if ($response->failed()) {
            \Illuminate\Support\Facades\Log::error('Chatbot Gemini API error', [
                'status' => $response->status(),
                'body' => $response->json() ?: $response->body(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Chatbot đang bận, vui lòng thử lại sau.',
            ], 502);
        }

        $reply = data_get($response->json(), 'candidates.0.content.parts.0.text');

        // Phân tích câu hỏi và truy vấn các sản phẩm thực tế từ Database
        $messageLower = mb_strtolower($validated['message']);
        $shouldRecommend = false;
        
        $keywords = [
            'tài chính', 'ngân sách', 'budget', 'bao nhiêu', 'giá', 'tiền', 
            '200k', '300k', '500k', '100k', '400k',
            'tư vấn', 'mẫu hoa', 'sản phẩm', 'hoa hồng', 'hoa lan', 'hoa tulip',
            'mua hoa', 'đặt hoa', 'hoa bó', 'hoa sinh nhật', 'hoa khai trương', 'hoa tốt nghiệp'
        ];
        
        foreach ($keywords as $keyword) {
            if (str_contains($messageLower, $keyword)) {
                $shouldRecommend = true;
                break;
            }
        }

        $recommendedProducts = [];
        if ($shouldRecommend) {
            $budget = null;
            if (preg_match('/(\d+)\s*(k|k\s*đ|trăm|triệu)/iu', $messageLower, $matches)) {
                $num = (int)$matches[1];
                $unit = mb_strtolower($matches[2]);
                if ($unit === 'k') {
                    $budget = $num * 1000;
                } elseif (str_contains($unit, 'trăm')) {
                    $budget = $num * 100000;
                }
            } elseif (preg_match('/(\d+)\s*(000)/', $messageLower, $matches)) {
                $budget = (int)$matches[0];
            }

            $productsQuery = \App\Models\Product::query()
                ->with(['variants'])
                ->where('is_active', true);

            if ($budget) {
                // Cho phép lệch +100k gợi ý thêm phân khúc cao hơn nhẹ
                $productsQuery->whereHas('variants', function($query) use ($budget) {
                    $query->where('price', '<=', $budget + 100000);
                });
            }

            $products = $productsQuery->get();
            
            if ($budget) {
                // Sắp xếp các sản phẩm có giá rẻ nhất lên đầu để vừa vặn ngân sách
                $products = $products->sortBy(function($product) {
                    $variant = $product->variants->first();
                    return $variant ? $variant->price : 99999999;
                });
            }
            
            $products = $products->take(4);
            
            // Nếu không tìm thấy, gợi ý 4 sản phẩm giá tốt có sẵn bất kỳ
            if ($products->isEmpty()) {
                $products = \App\Models\Product::query()
                    ->with(['variants'])
                    ->where('is_active', true)
                    ->get()
                    ->sortBy(function($product) {
                        $variant = $product->variants->first();
                        return $variant ? $variant->price : 99999999;
                    })
                    ->take(4);
            }

            $recommendedProducts = $products->map(function($product) {
                $variant = $product->variants->first();
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $variant ? $variant->price : 0,
                    'sale_price' => $variant ? $variant->sale_price : null,
                    'img' => $product->thumbnail ?: 'product-default.jpg',
                ];
            })->values()->toArray();
        }

        return response()->json([
            'status' => 'success',
            'reply' => $reply ?: 'Xin lỗi, mình chưa có câu trả lời phù hợp. Bạn có thể chọn Chat trực tuyến để được nhân viên hỗ trợ.',
            'products' => $recommendedProducts,
        ]);
    }
}
