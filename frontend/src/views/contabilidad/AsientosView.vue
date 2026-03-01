<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Asientos Contables</h2>
      <router-link to="/app/asientos/nuevo" class="btn-primary">+ Nuevo Asiento</router-link>
    </div>
    <div class="bg-white rounded-xl shadow p-4 mb-4 flex gap-4">
      <div><label class="label">Desde</label><input v-model="filtros.desde" type="date" class="input" /></div>
      <div><label class="label">Hasta</label><input v-model="filtros.hasta" type="date" class="input" /></div>
      <div class="flex items-end"><button @click="loadAsientos" class="btn-primary">Buscar</button></div>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr>
          <th class="th">Número</th><th class="th">Fecha</th><th class="th">Concepto</th><th class="th">Módulo</th><th class="th">Debe</th><th class="th">Haber</th>
        </tr></thead>
        <tbody>
          <tr v-for="a in asientos" :key="a.id" class="border-t hover:bg-gray-50">
            <td class="td font-mono text-sm">{{ a.numero }}</td>
            <td class="td">{{ a.fecha }}</td>
            <td class="td">{{ a.concepto }}</td>
            <td class="td"><span v-if="a.origen_modulo" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ a.origen_modulo }}</span></td>
            <td class="td text-right">{{ totalDebe(a).toFixed(2) }}</td>
            <td class="td text-right">{{ totalHaber(a).toFixed(2) }}</td>
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
const asientos = ref([])
const filtros = ref({ desde: new Date().toISOString().slice(0,7)+'-01', hasta: new Date().toISOString().slice(0,10) })
onMounted(loadAsientos)
async function loadAsientos() {
  const r = await api.get('/asientos', { params: { empresa_id: auth.empresaActual?.id, ...filtros.value } })
  asientos.value = r.data
}
const totalDebe = (a) => a.detalles?.reduce((s,d) => s + parseFloat(d.debe||0), 0) || 0
const totalHaber = (a) => a.detalles?.reduce((s,d) => s + parseFloat(d.haber||0), 0) || 0
</script>
