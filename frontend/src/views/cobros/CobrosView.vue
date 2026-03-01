<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Cobros</h2>
      <router-link to="/app/cobros/nuevo" class="btn-primary">+ Nuevo Cobro</router-link>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr>
          <th class="th">Fecha</th><th class="th">Cliente</th><th class="th">Concepto</th><th class="th">Total</th>
        </tr></thead>
        <tbody>
          <tr v-for="c in cobros" :key="c.id" class="border-t hover:bg-gray-50">
            <td class="td">{{ c.fecha }}</td>
            <td class="td">{{ c.cliente?.nombres }}</td>
            <td class="td">{{ c.concepto }}</td>
            <td class="td text-right font-semibold">${{ parseFloat(c.total).toFixed(2) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
const cobros = ref([])
onMounted(async () => { const r = await api.get('/cobros', { params: { empresa_id: auth.empresaActual?.id } }); cobros.value = r.data })
</script>
