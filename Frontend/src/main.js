import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

// FIX: Cấu hình Axios để tự động đính kèm Bearer Token
import axios from 'axios'

axios.interceptors.request.use((config) => {
  const token = localStorage.getItem('access_token')

  config.headers.Accept = 'application/json'

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('access_token')

      if (window.location.pathname.startsWith('/admin')) {
        window.location.href = '/login'
      }
    }

    return Promise.reject(error)
  }
)


import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
