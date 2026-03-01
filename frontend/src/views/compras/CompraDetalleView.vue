<template>
  <div class="p-6">
    <div class="flex items-center gap-4 mb-6">
      <router-link to="/app/compras" class="text-blue-600 hover:underline">← Volver</router-link>
      <h2 class="text-2xl font-bold text-gray-800">Compra {{ compra?.numero }}</h2>
    </div>
    <div v-if="compra" class="space-y-4">
      <div class="bg-white rounded-xl shadow p-6">
        <div class="grid grid-cols-3 gap-4 text-sm">
          <div><span class="font-medium text-gray-500">Proveedor:</span><p>{{ compra.proveedor?.nombres }}</p></div>
          <div><span class="font-medium text-gray-500">Fecha:</span><p>{{ compra.fecha }}</p></div>
          <div><span class="font-medium text-gray-500">Número:</span><p>{{ compra.numero }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold mb-3">Detalle</h3>
        <table class="w-full text-sm">
          <thead class="bg-gray-50"><tr><th class="th">Descripción</th><th class="th">Cant.</th><th class="th">Precio</th><th class="th">IVA</th><th class="th">Total</th></tr></thead>
          <tbody>
            <tr v-for="d in compra.detalles" :key="d.id" class="border-t">
              <td class="td">{{ d.descripcion }}</td><td class="td text-right">{{ d.cantidad }}</td>
              <td class="td text-right">${{ parseFloat(d.precio_unitario).toFixed(2) }}</td>
              <td class="td text-right">${{ parseFloat(d.iva).toFixed(2) }}</td>
              <td class="td text-right font-medium">${{ parseFloat(d.total).toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="bg-white rounded-xl shadow p-6 flex justify-end">
        <div class="w-64 text-sm">
          <div class="flex justify-between py-2 border-t font-bold text-lg"><span>TOTAL:</span><span>${{ parseFloat(compra.total).toFixed(2) }}</span></div>
          <div class="flex justify-between py-1 text-red-600"><span>Saldo:</span><span>${{ parseFloat(compra.saldo).toFixed(2) }}</span></div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'
const route = useRoute()
const compra = ref(null)
onMounted(async () => { const r = await api.get(`/compras/${route.params.id}`); compra.value = r.data })
</script>
