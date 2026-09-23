import { defineStore } from 'pinia';
import http from '@/utils/http';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    subtotal: 0,
    loaded: false,
    loading: false
  }),
  getters: {
    count: (state) => state.items.reduce((n, i) => n + Number(i.quantity), 0)
  },
  actions: {
    async fetchCart(force = false) {
      if (this.loaded && !force) return;
      this.loading = true;
      try {
        const res = await http.get('/api/cart');
        this.applyPayload(res.data.data);
      } finally {
        this.loading = false;
      }
    },
    async addToCart(variantId, quantity = 1) {
      const res = await http.post('/api/cart/items', {
        product_variant_id: variantId,
        quantity
      });
      this.applyPayload(res.data.data);
    },
    async updateQty(itemId, quantity) {
      const res = await http.patch(`/api/cart/items/${itemId}`, { quantity });
      this.applyPayload(res.data.data);
    },
    async removeItem(itemId) {
      const res = await http.delete(`/api/cart/items/${itemId}`);
      this.applyPayload(res.data.data);
    },
    async clearCart() {
      const res = await http.delete('/api/cart');
      this.applyPayload(res.data.data);
    },
    applyPayload(payload) {
      this.items = payload?.items || [];
      this.subtotal = Number(payload?.subtotal || 0);
      this.loaded = true;
    },
    reset() {
      this.items = [];
      this.subtotal = 0;
      this.loaded = false;
    }
  }
});
