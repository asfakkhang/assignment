<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const form = ref({
  email: '',
  password: ''
})

const submit = async () => {
  await auth.login(form.value)
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-6 rounded shadow w-80">
      <h1 class="font-bold mb-4">Login</h1>

      <p v-if="auth.errorMessage" class="text-red-500 mb-2" style="color:red;">{{ auth.errorMessage }}</p>

      <input v-model="form.email" placeholder="Email" class="input mb-2" />
      <input v-model="form.password" type="password" placeholder="Password" class="input mb-4" />

      <button @click="submit" class="btn-primary w-full">
        Login
      </button>
    </div>
  </div>
</template>
