<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({ symbol: String })
const orderbook = ref({ bids: [], asks: [] })

watch(() => props.symbol, async () => {
  const { data } = await axios.get(`/api/orderbook/${props.symbol}`)
  orderbook.value = data
})
</script>
