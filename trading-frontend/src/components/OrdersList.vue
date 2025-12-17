<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useOrdersStore } from '@/stores/orders'

const ordersStore = useOrdersStore()
const selectedSymbol = ref('')

// Fetch orders initially
const fetchOrders = async (symbol = '') => {
  await ordersStore.fetchOrders(symbol)
}

// Initial fetch on mount
onMounted(() => {
  fetchOrders()
})

// Watch dropdown changes
watch(selectedSymbol, (symbol) => {
  fetchOrders(symbol)
})

// Cancel order function
const cancelOrder = async (orderId) => {
  alert(orderId);
  try {
    const { data } = await axios.post(`/api/orders/${orderId}/cancel`, {}, {
      withCredentials: true
    })

    if (data.success) {
      alert(data.message)            // success message
      await fetchOrders(selectedSymbol.value) // refresh orders after cancel
    } else {
      alert(data.message)            // failure message
    }
  } catch (err) {
    alert(err.response?.data?.message || 'Something went wrong')
  }
}
</script>

<template>
  <div class="bg-white p-4 rounded shadow">
    <h2 class="font-bold mb-3">Orders</h2>

    <table class="w-full text-sm">
      <thead>
        <tr class="border-b">
          <th>Symbol</th>
          <th>Side</th>
          <th>Price</th>
          <th>Amount</th>
          <th>
            <select v-model="selectedSymbol">
              <option value="">All</option>
              <option value="BTC">BTC</option>
              <option value="ETH">ETH</option>
            </select>
            </th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="order in ordersStore.orders"
          :key="order.id"
          class="border-b"
        >
          <td>{{ order.symbol }}</td>
          <td
            :class="order.side === 'buy'
              ? 'text-green-600'
              : 'text-red-600'"
          >
            {{ order.side }}
          </td>
          <td>{{ order.price }}</td>
          <td>{{ order.amount }}</td>
          <td>
            <button
              v-if="order.status === 1"
              @click="cancelOrder(order.id)"
              class="text-red-500 hover:underline ml-2"
            >
              Cancel
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-if="!ordersStore.orders.length" class="text-gray-500 text-center">
      No orders found
    </p>
  </div>
</template>
