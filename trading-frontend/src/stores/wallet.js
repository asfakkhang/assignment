import { defineStore } from 'pinia'
import axios from 'axios'

export const useWalletStore = defineStore('wallet', {
  state: () => ({
    usd: '0.00',

    assets: {
      BTC: {
        available: '0',
        locked: '0',
        total: '0'
      },
      ETH: {
        available: '0',
        locked: '0',
        total: '0'
      }
    },
    listening: false
  }),

  actions: {
    async fetchProfile() {
      if (this.listening) return
      this.listening = true
      const { data } = await axios.get('/api/profile')

      this.usd = data.usd_balance

      // assets may or may not exist (safe assign)
      if (data.assets?.BTC) {
        this.assets.btc = data.assets.BTC
      }

      if (data.assets?.ETH) {
        this.assets.eth = data.assets.ETH
      }
    },

    /**
     * Realtime sync from OrderMatched event
     */
    sync(balances) {
      this.usd = balances.usd_balance

      if (balances.assets?.BTC) {
        this.assets.btc = balances.assets.BTC
      }

      if (balances.assets?.ETH) {
        this.assets.eth = balances.assets.ETH
      }
    }
  }
})
