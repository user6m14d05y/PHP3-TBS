#!/bin/bash
# E2E test cho module mua hàng — chạy trong WSL
# Usage: bash e2e-test-order.sh
set -e

API="http://localhost:8888/api"
PASS=0
FAIL=0

check() {
  local name="$1"
  local actual="$2"
  local expected="$3"
  if [[ "$actual" == *"$expected"* ]]; then
    PASS=$((PASS+1))
    echo "PASS: $name"
  else
    FAIL=$((FAIL+1))
    echo "FAIL: $name — expected '$expected', got: $(echo "$actual" | head -c 300)"
  fi
}

# 1. Login user
RESP=$(curl -s -X POST "$API/Login" -H 'Content-Type: application/json' -d '{"email":"test@example.com","password":"password"}')
check "login user" "$RESP" '"status":"success"'
TOKEN=$(echo "$RESP" | python3 -c 'import sys,json; print(json.load(sys.stdin)["access_token"])')
AUTH="Authorization: Bearer $TOKEN"

# 2. Giỏ hàng ban đầu
RESP=$(curl -s "$API/cart" -H "$AUTH" -H 'Accept: application/json')
check "get cart" "$RESP" '"status":"success"'
# Dọn giỏ hàng cũ (nếu có) để test sạch
curl -s -X DELETE "$API/cart" -H "$AUTH" -H 'Accept: application/json' > /dev/null

# 3. Thêm sản phẩm vào giỏ
RESP=$(curl -s -X POST "$API/cart/items" -H "$AUTH" -H 'Accept: application/json' -H 'Content-Type: application/json' -d '{"product_variant_id":1,"quantity":1}')
check "add to cart" "$RESP" '"status":"success"'
ITEM_ID=$(echo "$RESP" | python3 -c 'import sys,json; d=json.load(sys.stdin); print(d["data"]["items"][0]["id"])')
SUBTOTAL=$(echo "$RESP" | python3 -c 'import sys,json; print(json.load(sys.stdin)["data"]["subtotal"])')
echo "  subtotal=$SUBTOTAL"

# 4. Tạo địa chỉ (gần shop Q1)
RESP=$(curl -s -X POST "$API/addresses" -H "$AUTH" -H 'Accept: application/json' -H 'Content-Type: application/json' -d '{
  "recipient_name":"Nguyen Van A",
  "phone":"0901234567",
  "address_line":"123 Le Loi",
  "ward":"Ben Nghe",
  "district":"Quan 1",
  "city":"TP. Ho Chi Minh",
  "latitude":10.7769,
  "longitude":106.7009,
  "formatted_address":"123 Le Loi, Ben Nghe, Quan 1, TP. Ho Chi Minh",
  "place_id":"test-place-1"
}')
check "create address" "$RESP" '"status":"success"'
ADDR_ID=$(echo "$RESP" | python3 -c 'import sys,json; print(json.load(sys.stdin)["data"]["id"])')
echo "  address_id=$ADDR_ID"

# 5. Delivery check với shop 1
RESP=$(curl -s -X POST "$API/shops/delivery-check" -H 'Content-Type: application/json' -d '{"shop_id":1,"latitude":10.7769,"longitude":106.7009}')
check "delivery check can_deliver" "$RESP" '"can_deliver":true'
check "delivery check has shipping_fees.standard" "$RESP" '"standard"'
check "delivery check has shipping_fees.express" "$RESP" '"express"'
STD_FEE=$(echo "$RESP" | python3 -c 'import sys,json; print(json.load(sys.stdin)["data"]["shipping_fees"]["standard"])')
EXP_FEE=$(echo "$RESP" | python3 -c 'import sys,json; print(json.load(sys.stdin)["data"]["shipping_fees"]["express"])')
echo "  standard=$STD_FEE express=$EXP_FEE"

# 6. Apply coupon WELCOME10
RESP=$(curl -s -X POST "$API/coupons/apply" -H "$AUTH" -H 'Accept: application/json' -H 'Content-Type: application/json' -d '{"coupon_code":"WELCOME10"}')
check "apply coupon" "$RESP" '"status":"success"'
DISCOUNT=$(echo "$RESP" | python3 -c 'import sys,json; print(json.load(sys.stdin)["data"]["discount_amount"])')
echo "  discount=$DISCOUNT"

# 7. Checkout với express + COD + coupon
RESP=$(curl -s -X POST "$API/checkout" -H "$AUTH" -H 'Accept: application/json' -H 'Content-Type: application/json' -d "{
  \"shop_id\":1,
  \"user_address_id\":$ADDR_ID,
  \"coupon_code\":\"WELCOME10\",
  \"payment_method\":\"cod\",
  \"shipping_method\":\"express\",
  \"note\":\"E2E test order\"
}")
check "checkout success" "$RESP" '"status":"success"'
ORDER_CODE=$(echo "$RESP" | python3 -c 'import sys,json; print(json.load(sys.stdin)["data"]["order_code"])')
echo "  order_code=$ORDER_CODE"
check "checkout shipping_method=express" "$RESP" '"shipping_method":"express"'
SHIP_FEE=$(echo "$RESP" | python3 -c 'import sys,json; print(json.load(sys.stdin)["data"]["shipping_fee"])')
FEE_OK=$(python3 -c "print(abs(float('$SHIP_FEE') - float('$EXP_FEE')) < 0.001)")
if [[ "$FEE_OK" == "True" ]]; then
  PASS=$((PASS+1)); echo "PASS: express fee matches preview ($SHIP_FEE)"
else
  FAIL=$((FAIL+1)); echo "FAIL: express fee $SHIP_FEE != preview $EXP_FEE"
fi

# 8. Giỏ hàng đã bị xóa sau checkout
RESP=$(curl -s "$API/cart" -H "$AUTH" -H 'Accept: application/json')
check "cart empty after checkout" "$RESP" '"items":[]'

# 9. Danh sách đơn hàng của user
RESP=$(curl -s "$API/orders" -H "$AUTH" -H 'Accept: application/json')
check "list orders" "$RESP" '"status":"success"'

# 10. Chi tiết đơn hàng
RESP=$(curl -s "$API/orders/$ORDER_CODE" -H "$AUTH" -H 'Accept: application/json')
check "order detail" "$RESP" '"status":"success"'

# 11. Hủy đơn (đang awaiting_payment)
RESP=$(curl -s -X POST "$API/orders/$ORDER_CODE/cancel" -H "$AUTH" -H 'Accept: application/json')
check "cancel order" "$RESP" '"status":"cancelled"'

# 12. Hủy lại lần 2 → vẫn OK (idempotent, đã cancelled)
RESP=$(curl -s -X POST "$API/orders/$ORDER_CODE/cancel" -H "$AUTH" -H 'Accept: application/json')
check "cancel again (idempotent)" "$RESP" '"status":"cancelled"'

# 13. Admin login + list orders
RESP=$(curl -s -X POST "$API/Login" -H 'Content-Type: application/json' -d '{"email":"admin@example.com","password":"password"}')
check "login admin" "$RESP" '"status":"success"'
ATOKEN=$(echo "$RESP" | python3 -c 'import sys,json; print(json.load(sys.stdin)["access_token"])')
AAUTH="Authorization: Bearer $ATOKEN"

RESP=$(curl -s "$API/admin/orders" -H "$AAUTH")
check "admin list orders" "$RESP" '"status":"success"'

# 14. Admin xem chi tiết đơn
RESP=$(curl -s "$API/admin/orders/$ORDER_CODE" -H "$AAUTH")
check "admin order detail" "$RESP" '"status":"success"'

# 15. Admin đẩy trạng thái sai thứ tự (cancelled -> shipping) → 422
RESP=$(curl -s -X PATCH "$API/admin/orders/$ORDER_CODE/status" -H "$AAUTH" -H 'Accept: application/json' -H 'Content-Type: application/json' -d '{"status":"shipping"}')
check "invalid transition rejected" "$RESP" '"errors"'

# 16. User khác không xem được đơn (đăng ký user mới)
RESP=$(curl -s -X POST "$API/register" -H 'Content-Type: application/json' -d '{"name":"Other User","email":"other2@example.com","password":"password123"}')
check "register other user" "$RESP" '"status":"success"'
OTOKEN=$(curl -s -X POST "$API/Login" -H 'Content-Type: application/json' -d '{"email":"other2@example.com","password":"password123"}' | python3 -c 'import sys,json; print(json.load(sys.stdin).get("access_token",""))')
OAUTH="Authorization: Bearer $OTOKEN"
HTTP_CODE=$(curl -s -o /dev/null -w '%{http_code}' "$API/orders/$ORDER_CODE" -H "$OAUTH")
if [[ "$HTTP_CODE" == "404" ]]; then
  PASS=$((PASS+1)); echo "PASS: other user gets 404 on foreign order"
else
  FAIL=$((FAIL+1)); echo "FAIL: other user got HTTP $HTTP_CODE, expected 404"
fi

echo ""
echo "=================================="
echo "TOTAL: PASS=$PASS FAIL=$FAIL"
echo "=================================="
