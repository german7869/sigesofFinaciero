<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Facturas de Venta</h2>
      <router-link to="/app/ventas/nueva" class="btn-primary">+ Nueva Factura</router-link>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr>
          <th class="th">Número</th><th class="th">Fecha</th><th class="th">Cliente</th><th class="th">Total</th><th class="th">Saldo</th><th class="th">Estado</th><th class="th">Acciones</th>
        </tr></thead>
        <tbody>
          <tr v-for="v in ventas" :key="v.id" class="border-t hover:bg-gray-50">
            <td class="td font-mono text-sm">{{ v.numero }}</td>
            <td class="td">{{ v.fecha }}</td>
            <td class="td">{{ v.cliente?.nombres }}</td>
            <td class="td text-right">${{ parseFloat(v.total).toFixed(2) }}</td>
            <td class="td text-right" :class="parseFloat(v.saldo) > 0 ? 'text-red-600 font-semibold' : 'text-green-600'">${{ parseFloat(v.saldo).toFixed(2) }}</td>
            <td class="td"><span class="text-xs px-2 py-1 rounded" :class="v.estado==='ACTIVA'?'bg-green-100 text-green-700':'bg-red-100 text-red-700'">{{ v.estado }}</span></td>
            <td class="td"><router-link :to="`/app/ventas/${v.id}`" class="text-blue-600 hover:underline">Ver</router-link></td>
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
const ventas = ref([])
onMounted(async () => { const r = await api.get('/ventas', { params: { empresa_id: auth.empresaActual?.id } }); ventas.value = r.data })
</script>
