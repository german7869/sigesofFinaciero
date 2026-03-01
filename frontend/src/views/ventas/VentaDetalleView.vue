<template>
  <div class="p-6">
    <div class="flex items-center gap-4 mb-6">
      <router-link to="/app/ventas" class="text-blue-600 hover:underline">← Volver</router-link>
      <h2 class="text-2xl font-bold text-gray-800">Factura {{ venta?.numero }}</h2>
      <span v-if="venta" class="text-sm px-3 py-1 rounded" :class="venta.estado==='ACTIVA'?'bg-green-100 text-green-700':'bg-red-100 text-red-700'">{{ venta?.estado }}</span>
    </div>
    <div v-if="venta" class="space-y-4">
      <div class="bg-white rounded-xl shadow p-6">
        <div class="grid grid-cols-3 gap-4 text-sm">
          <div><span class="font-medium text-gray-500">Cliente:</span><p>{{ venta.cliente?.nombres }}</p></div>
          <div><span class="font-medium text-gray-500">Fecha:</span><p>{{ venta.fecha }}</p></div>
          <div><span class="font-medium text-gray-500">Documento:</span><p>{{ venta.documento?.descripcion }}</p></div>
          <div class="col-span-3"><span class="font-medium text-gray-500">Concepto:</span><p>{{ venta.concepto }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold mb-3">Detalle</h3>
        <table class="w-full text-sm">
          <thead class="bg-gray-50"><tr><th class="th">Descripción</th><th class="th">Cant.</th><th class="th">Precio</th><th class="th">IVA</th><th class="th">Total</th></tr></thead>
          <tbody>
            <tr v-for="d in venta.detalles" :key="d.id" class="border-t">
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
          <div class="flex justify-between py-1"><span>Subtotal 15%:</span><span>${{ parseFloat(venta.subtotal_15).toFixed(2) }}</span></div>
          <div class="flex justify-between py-1"><span>IVA 15%:</span><span>${{ parseFloat(venta.iva_15).toFixed(2) }}</span></div>
          <div class="flex justify-between py-1"><span>Subtotal 0%:</span><span>${{ parseFloat(venta.subtotal_0).toFixed(2) }}</span></div>
          <div class="flex justify-between py-2 border-t font-bold text-lg"><span>TOTAL:</span><span>${{ parseFloat(venta.total).toFixed(2) }}</span></div>
          <div class="flex justify-between py-1 text-red-600"><span>Saldo:</span><span>${{ parseFloat(venta.saldo).toFixed(2) }}</span></div>
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
const venta = ref(null)
onMounted(async () => { const r = await api.get(`/ventas/${route.params.id}`); venta.value = r.data })
</script>
