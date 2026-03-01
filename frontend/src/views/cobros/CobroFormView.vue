<template>
  <div class="p-6">
    <div class="flex items-center gap-4 mb-6">
      <router-link to="/app/cobros" class="text-blue-600 hover:underline">← Volver</router-link>
      <h2 class="text-2xl font-bold text-gray-800">Nuevo Cobro</h2>
    </div>
    <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">{{ error }}</div>
    <div class="bg-white rounded-xl shadow p-6 mb-4">
      <div class="grid grid-cols-3 gap-4 mb-4">
        <div><label class="label">Cliente *</label>
          <select v-model.number="form.cliente_id" @change="loadVentas" required class="input">
            <option v-for="c in clientes" :key="c.id" :value="c.id">{{ c.identificacion }} - {{ c.nombres }}</option>
          </select>
        </div>
        <div><label class="label">Fecha *</label><input v-model="form.fecha" type="date" required class="input" /></div>
        <div><label class="label">Concepto</label><input v-model="form.concepto" class="input" /></div>
      </div>
      <!-- Facturas pendientes -->
      <div v-if="ventasPendientes.length > 0">
        <h3 class="font-semibold mb-3">Facturas Pendientes</h3>
        <table class="w-full mb-4 text-sm">
          <thead class="bg-gray-50"><tr><th class="th">Sel</th><th class="th">Número</th><th class="th">Fecha</th><th class="th">Total</th><th class="th">Saldo</th><th class="th w-32">A Cobrar</th></tr></thead>
          <tbody>
            <tr v-for="v in ventasPendientes" :key="v.id" class="border-t">
              <td class="td"><input type="checkbox" v-model="v._selected" @change="toggleVenta(v)" /></td>
              <td class="td font-mono">{{ v.numero }}</td>
              <td class="td">{{ v.fecha }}</td>
              <td class="td text-right">${{ parseFloat(v.total).toFixed(2) }}</td>
              <td class="td text-right text-red-600">${{ parseFloat(v.saldo).toFixed(2) }}</td>
              <td class="td"><input v-if="v._selected" v-model.number="v._monto" type="number" step="0.01" :max="v.saldo" class="input text-sm text-right" /></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 mb-4">
      <h3 class="font-semibold mb-3">Forma de Pago</h3>
      <table class="w-full mb-4">
        <thead class="bg-gray-50"><tr><th class="th">Forma</th><th class="th">Descripción</th><th class="th w-32">Monto</th><th class="th w-8"></th></tr></thead>
        <tbody>
          <tr v-for="(p, i) in form.pagos" :key="i">
            <td class="td">
              <select v-model.number="p.forma_pago_id" class="input text-sm">
                <option :value="null">-- libre --</option>
                <option v-for="fp in formasPago" :key="fp.id" :value="fp.id">{{ fp.nombre }}</option>
              </select>
            </td>
            <td class="td"><input v-model="p.descripcion" required class="input text-sm" /></td>
            <td class="td"><input v-model.number="p.monto" type="number" step="0.01" class="input text-sm text-right" /></td>
            <td class="td"><button @click="form.pagos.splice(i,1)" class="text-red-500">✕</button></td>
          </tr>
        </tbody>
      </table>
      <button @click="form.pagos.push({forma_pago_id: null, descripcion: 'Cobro', monto: totalCobro})" class="btn-secondary">+ Agregar Pago</button>
    </div>
    <div class="bg-white rounded-xl shadow p-4 mb-6 text-right font-bold text-lg">
      Total a Cobrar: ${{ totalCobro.toFixed(2) }}
    </div>
    <div class="flex justify-end gap-3">
      <router-link to="/app/cobros" class="btn-secondary">Cancelar</router-link>
      <button @click="save" :disabled="saving" class="btn-primary">{{ saving ? 'Guardando...' : 'Registrar Cobro' }}</button>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
const router = useRouter()
const clientes = ref([])
const ventasPendientes = ref([])
const formasPago = ref([])
const saving = ref(false)
const error = ref('')
const form = ref({
  empresa_id: auth.empresaActual?.id,
  cliente_id: null, fecha: new Date().toISOString().slice(0,10), concepto: '',
  detalles: [], pagos: []
})
const totalCobro = computed(() => ventasPendientes.value.filter(v => v._selected).reduce((s,v) => s + (v._monto||0), 0))
onMounted(async () => {
  const [r1, r2] = await Promise.all([
    api.get('/clientes', { params: { empresa_id: auth.empresaActual?.id } }),
    api.get('/formas-pago', { params: { empresa_id: auth.empresaActual?.id } })
  ])
  clientes.value = r1.data; formasPago.value = r2.data
})
async function loadVentas() {
  if (!form.value.cliente_id) return
  const r = await api.get('/ventas', { params: { empresa_id: auth.empresaActual?.id, cliente_id: form.value.cliente_id } })
  ventasPendientes.value = r.data.filter(v => parseFloat(v.saldo) > 0).map(v => ({ ...v, _selected: false, _monto: parseFloat(v.saldo) }))
}
function toggleVenta(v) {
  if (!v._selected) { v._monto = 0 } else { v._monto = parseFloat(v.saldo) }
}
async function save() {
  error.value = ''; saving.value = true
  const detalles = ventasPendientes.value.filter(v => v._selected && v._monto > 0).map(v => ({ venta_id: v.id, monto: v._monto }))
  if (!detalles.length) { error.value = 'Seleccione al menos una factura'; saving.value = false; return }
  try {
    await api.post('/cobros', { ...form.value, detalles })
    router.push('/app/cobros')
  } catch(e) { error.value = e.response?.data?.message || 'Error al guardar' }
  finally { saving.value = false }
}
</script>
