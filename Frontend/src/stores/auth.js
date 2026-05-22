import { defineStore } from 'pinia';
import axios from 'axios';
import { apiUrl } from '@/utils/api';

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
        const res = await axios.get(apiUrl('/api/me'), {
            headers: { Authorization: `Bearer ${token}` }
        });
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
