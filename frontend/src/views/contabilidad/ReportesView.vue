<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Reportes Contables</h2>
    <div class="grid grid-cols-2 gap-6">
      <!-- Libro Diario -->
      <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-lg mb-4">Libro Diario</h3>
        <div class="flex gap-3 mb-4">
          <div><label class="label">Desde</label><input v-model="libroDiario.desde" type="date" class="input" /></div>
          <div><label class="label">Hasta</label><input v-model="libroDiario.hasta" type="date" class="input" /></div>
        </div>
        <button @click="loadLibroDiario" class="btn-primary mb-4">Consultar</button>
        <div class="overflow-auto max-h-96">
          <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr><th class="th">Fecha</th><th class="th">Concepto</th><th class="th">Cuenta</th><th class="th">Debe</th><th class="th">Haber</th></tr></thead>
            <tbody>
              <template v-for="a in libroDiario.data" :key="a.id">
                <tr v-for="d in a.detalles" :key="d.id" class="border-t">
                  <td class="td">{{ a.fecha }}</td>
                  <td class="td">{{ a.concepto }}</td>
                  <td class="td font-mono">{{ d.cuenta?.codigo }} {{ d.cuenta?.descripcion }}</td>
                  <td class="td text-right">{{ parseFloat(d.debe) > 0 ? parseFloat(d.debe).toFixed(2) : '' }}</td>
                  <td class="td text-right">{{ parseFloat(d.haber) > 0 ? parseFloat(d.haber).toFixed(2) : '' }}</td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Mayor -->
      <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-lg mb-4">Mayor de Cuenta</h3>
        <div class="mb-3">
          <label class="label">Cuenta</label>
          <select v-model="mayor.cuenta_id" class="input">
            <option v-for="c in cuentas" :key="c.id" :value="c.id">{{ c.codigo }} - {{ c.descripcion }}</option>
          </select>
        </div>
        <div class="flex gap-3 mb-4">
          <div><label class="label">Desde</label><input v-model="mayor.desde" type="date" class="input" /></div>
          <div><label class="label">Hasta</label><input v-model="mayor.hasta" type="date" class="input" /></div>
        </div>
        <button @click="loadMayor" class="btn-primary mb-4">Consultar</button>
        <div class="overflow-auto max-h-96">
          <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr><th class="th">Fecha</th><th class="th">Concepto</th><th class="th">Debe</th><th class="th">Haber</th></tr></thead>
            <tbody>
              <tr v-for="d in mayor.data" :key="d.id" class="border-t">
                <td class="td">{{ d.asiento?.fecha }}</td>
                <td class="td">{{ d.asiento?.concepto }}</td>
                <td class="td text-right">{{ parseFloat(d.debe).toFixed(2) }}</td>
                <td class="td text-right">{{ parseFloat(d.haber).toFixed(2) }}</td>
              </tr>
            </tbody>
            <tfoot class="font-semibold bg-gray-50">
              <tr><td colspan="2" class="td text-right">TOTAL:</td>
                <td class="td text-right">{{ mayor.data.reduce((s,d)=>s+parseFloat(d.debe),0).toFixed(2) }}</td>
                <td class="td text-right">{{ mayor.data.reduce((s,d)=>s+parseFloat(d.haber),0).toFixed(2) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
const cuentas = ref([])
const libroDiario = ref({ desde: new Date().toISOString().slice(0,7)+'-01', hasta: new Date().toISOString().slice(0,10), data: [] })
const mayor = ref({ cuenta_id: null, desde: new Date().toISOString().slice(0,7)+'-01', hasta: new Date().toISOString().slice(0,10), data: [] })
onMounted(async () => { const r = await api.get('/cuentas-contables/flat', { params: { empresa_id: auth.empresaActual?.id } }); cuentas.value = r.data })
async function loadLibroDiario() {
  const r = await api.get('/reportes/libro-diario', { params: { empresa_id: auth.empresaActual?.id, desde: libroDiario.value.desde, hasta: libroDiario.value.hasta } })
  libroDiario.value.data = r.data
}
async function loadMayor() {
  const r = await api.get('/reportes/mayor-cuenta', { params: { empresa_id: auth.empresaActual?.id, cuenta_contable_id: mayor.value.cuenta_id, desde: mayor.value.desde, hasta: mayor.value.hasta } })
  mayor.value.data = r.data
}
</script>
