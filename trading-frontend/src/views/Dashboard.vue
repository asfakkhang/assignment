<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useOrdersStore } from '@/stores/orders'
import { useWalletStore } from '@/stores/wallet'

import OrderForm from '@/components/OrderForm.vue'
import OrdersList from '@/components/OrdersList.vue'
import Wallet from '@/components/Wallet.vue'
import Orderbook from '@/components/Orderbook.vue'

const auth = useAuthStore()
const orders = useOrdersStore()
const wallet = useWalletStore()

onMounted(async () => {
  await wallet.fetchProfile()
  await orders.fetchOrders()
  if (auth.user) {
    orders.listen(auth.user.id)
  }
  
})
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-xl font-bold">Dashboard</h1>
      <button @click="auth.logout" class="text-red-500">Logout</button>
    </div>

    <Wallet />

    <div class="grid grid-cols-2 gap-6">
      <OrderForm />
      <Orderbook symbol="BTC" />
    </div>

    <OrdersList />
  </div>
</template>
