import axios from 'axios';
import { apiBaseUrl } from './api';

const http = axios.create({
  baseURL: apiBaseUrl,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Thêm token vào tất cả request
http.interceptors.request.use((config) => {
  const token = localStorage.getItem('access_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});

// Xử lý lỗi tập trung
http.interceptors.response.use((response) => {
  return response;
}, (error) => {
  if (error.response && error.response.status === 401) {
    // Tự động xoá token nếu hết hạn
    localStorage.removeItem('access_token');
  }
  return Promise.reject(error);
});

// Lấy thông báo lỗi thân thiện từ response Laravel (422 errors object / message)
export const getErrorMessage = (error, fallback = 'Đã có lỗi xảy ra, vui lòng thử lại.') => {
  const data = error?.response?.data;
  if (data?.errors && typeof data.errors === 'object') {
    const firstKey = Object.keys(data.errors)[0];
    if (firstKey && Array.isArray(data.errors[firstKey]) && data.errors[firstKey].length > 0) {
      return data.errors[firstKey][0];
    }
  }
  if (data?.message && typeof data.message === 'string' && data.message !== 'The given data was invalid.') {
    return data.message;
  }
  return fallback;
};

export default http;
