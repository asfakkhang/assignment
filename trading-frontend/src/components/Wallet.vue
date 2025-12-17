<script setup>
import { onMounted } from 'vue'
import { useWalletStore } from '@/stores/wallet'

const wallet = useWalletStore()
onMounted(wallet.fetchProfile)
</script>

<template>
  <div class="bg-gray-50 p-4 rounded" style="text-align:left;">
    <p>USD: {{ wallet.usd }}</p>
    <p>BTC: {{ wallet.assets.btc }}</p>
    <p>ETH: {{ wallet.assets.eth }}</p>
  </div>
</template>
