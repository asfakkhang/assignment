import { defineStore } from 'pinia'
import axios from 'axios'
import router from '@/router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    errorMessage: ''
  }),

  actions: {
    async login(credentials) {
      try {
        this.errorMessage = ''

        // CSRF cookie for SPA login
        await axios.get('/sanctum/csrf-cookie', { withCredentials: true })
        
        const { data } = await axios.post('/api/login', credentials, {
          withCredentials: true
        });

        this.user = data.user
        this.token = data.token

        localStorage.setItem('token', data.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`

        router.push('/dashboard')
      } catch(err) {
        if (err?.response?.status === 401 && err?.response?.data?.message) {
          this.errorMessage = err.response.data.message
        } else if (err?.response?.status === 422) {
          const firstFieldErrors = Object.values(err.response.data.errors)
          this.errorMessage = firstFieldErrors[0][0]
        } else {
          this.errorMessage = 'Login failed. Please try again.'
        }
        console.error('Login error:', this.errorMessage)
      }
    },

    async logout() {
      await axios.post('/api/logout')

      this.user = null
      this.token = null
      localStorage.removeItem('token')

      delete axios.defaults.headers.common['Authorization']
      router.push('/')
    },

    init() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      }
    }
  }
})
