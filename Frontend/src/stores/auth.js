import { defineStore } from 'pinia';
import axios from 'axios';

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
        const res = await axios.get('http://localhost:8888/api/me', {
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
