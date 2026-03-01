<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Facturas de Compra</h2>
      <router-link to="/app/compras/nueva" class="btn-primary">+ Nueva Compra</router-link>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr>
          <th class="th">Número</th><th class="th">Fecha</th><th class="th">Proveedor</th><th class="th">Total</th><th class="th">Saldo</th><th class="th">Acciones</th>
        </tr></thead>
        <tbody>
          <tr v-for="c in compras" :key="c.id" class="border-t hover:bg-gray-50">
            <td class="td font-mono text-sm">{{ c.numero }}</td>
            <td class="td">{{ c.fecha }}</td>
            <td class="td">{{ c.proveedor?.nombres }}</td>
            <td class="td text-right">${{ parseFloat(c.total).toFixed(2) }}</td>
            <td class="td text-right" :class="parseFloat(c.saldo) > 0 ? 'text-red-600 font-semibold' : 'text-green-600'">${{ parseFloat(c.saldo).toFixed(2) }}</td>
            <td class="td"><router-link :to="`/app/compras/${c.id}`" class="text-blue-600 hover:underline">Ver</router-link></td>
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
const compras = ref([])
onMounted(async () => { const r = await api.get('/compras', { params: { empresa_id: auth.empresaActual?.id } }); compras.value = r.data })
</script>
