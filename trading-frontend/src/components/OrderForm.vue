<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useOrdersStore } from '@/stores/orders'

const ordersStore = useOrdersStore()

const form = ref({
  symbol: 'BTC',
  side: 'buy',
  price: '',
  amount: ''
})

const successMessage = ref('')
const errorMessage = ref('')

const submit = async () => {
  successMessage.value = ''
  errorMessage.value = ''

  const res = await ordersStore.placeOrder(form.value)

  if (res.success) {
    successMessage.value = res.message
  } else {
    errorMessage.value = res.message
  }
}
</script>

<template>
  <div class="bg-white p-4 rounded shadow">
    <h2 class="font-bold mb-3">Limit Order</h2>

    <div v-if="successMessage" class="mb-3 p-2 text-green-700 bg-green-100 rounded">
      {{ successMessage }}
    </div>

    <div v-if="errorMessage" class="mb-3 p-2 text-red-700 bg-red-100 rounded">
      {{ errorMessage }}
    </div>

    <select v-model="form.symbol" class="input">
      <option>BTC</option>
      <option>ETH</option>
    </select>

    <select v-model="form.side" class="input">
      <option value="buy">Buy</option>
      <option value="sell">Sell</option>
    </select>

    <input v-model="form.price" type="number" placeholder="Price" class="input" />
    <input v-model="form.amount" type="number" placeholder="Amount" class="input" />

    <button @click="submit" class="btn-primary">
      Place Order
    </button>
  </div>
</template>
