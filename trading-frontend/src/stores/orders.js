import { defineStore } from 'pinia'
import axios from 'axios'
import echo from '@/bootstrap/echo'
import { useWalletStore } from './wallet'

export const useOrdersStore = defineStore('orders', {
  state: () => ({
    orders: [],
    listening: false
  }),

  actions: {
    async fetchOrders(symbol='') {
      try {
        const { data } = await axios.get('/api/orders', {
          params: { symbol }
        })

        this.orders = data.data.orders

      } catch (err) {
        console.error('Fetch orders failed:', err)

        // fallback (UI break na ho)
        this.orders = []
      }
    },

    async placeOrder(payload) {
      try {
        const { data } = await axios.post('/api/orders', payload)

        return {
          success: true,
          message: data.message
        }

      } catch (err) {
        const error = err // 👈 important
        let message = 'Something went wrong'

        if (error.response && error.response.data && error.response.data.message) {
          message = error.response.data.message
        }

        return {
          success: false,
          message
        }
      }
    },

    listen(userId) {
      if (this.listening) return
      this.listening = true

      const wallet = useWalletStore()

      echo.private(`user.${userId}`)
        .listen('OrderMatched', (e) => {
          const order = e.data.order
          const balances = e.data.balances

          if (order) this.patchOrder(order)
          if (balances) wallet.sync(balances)
        })
    },

    patchOrder(updated) {
      const index = this.orders.findIndex(o => o.id === updated.id)

      if (index !== -1) {
        this.orders[index] = updated
      } else {
        this.orders.unshift(updated)
      }
    }
  }
})
