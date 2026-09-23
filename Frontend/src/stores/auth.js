import { defineStore } from 'pinia';
import http from '@/utils/http';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isLoaded: false
  }),
  actions: {
    async fetchUser() {
      const token = localStorage.getItem('access_token');
      if (!token) {
        this.user = null;
        this.isLoaded = true;
        return null;
      }
      try {
        const res = await http.get('/api/me');
        this.user = res.data;
      } catch (error) {
        this.user = null;
        localStorage.removeItem('access_token');
      }
      this.isLoaded = true;
      return this.user;
    },
    logout() {
      this.user = null;
      this.isLoaded = false;
      localStorage.removeItem('access_token');
    }
  }
});
