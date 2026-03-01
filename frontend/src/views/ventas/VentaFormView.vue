<template>
  <div class="p-6">
    <div class="flex items-center gap-4 mb-6">
      <router-link to="/app/ventas" class="text-blue-600 hover:underline">← Volver</router-link>
      <h2 class="text-2xl font-bold text-gray-800">Nueva Factura de Venta</h2>
    </div>
    <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">{{ error }}</div>
    <div class="bg-white rounded-xl shadow p-6 mb-4">
      <div class="grid grid-cols-3 gap-4">
        <div><label class="label">Cliente *</label>
          <select v-model.number="form.cliente_id" required class="input">
            <option v-for="c in clientes" :key="c.id" :value="c.id">{{ c.identificacion }} - {{ c.nombres }}</option>
          </select>
        </div>
        <div><label class="label">Documento *</label>
          <select v-model.number="form.documento_id" required class="input">
            <option v-for="d in documentos" :key="d.id" :value="d.id">{{ d.codigo }} - {{ d.descripcion }}</option>
          </select>
        </div>
        <div><label class="label">Fecha *</label><input v-model="form.fecha" type="date" required class="input" /></div>
        <div class="col-span-3"><label class="label">Concepto</label><input v-model="form.concepto" class="input" /></div>
      </div>
    </div>
    <!-- Detalles -->
    <div class="bg-white rounded-xl shadow p-6 mb-4">
      <h3 class="font-semibold mb-3">Detalle de Productos</h3>
      <table class="w-full mb-4">
        <thead class="bg-gray-50"><tr>
          <th class="th">Producto</th><th class="th">Descripción</th><th class="th w-20">Cant.</th><th class="th w-28">Precio</th><th class="th w-24">Desc.</th><th class="th">IVA</th><th class="th w-28">Subtotal</th><th class="th w-8"></th>
        </tr></thead>
        <tbody>
          <tr v-for="(d, i) in form.detalles" :key="i">
            <td class="td">
              <select v-model.number="d.producto_id" @change="onProductoChange(d)" class="input text-sm">
                <option :value="null">-- libre --</option>
                <option v-for="p in productos" :key="p.id" :value="p.id">{{ p.nombre }}</option>
              </select>
            </td>
            <td class="td"><input v-model="d.descripcion" required class="input text-sm" /></td>
            <td class="td"><input v-model.number="d.cantidad" type="number" step="0.01" min="0" class="input text-sm text-right" @input="calcLine(d)" /></td>
            <td class="td"><input v-model.number="d.precio_unitario" type="number" step="0.01" min="0" class="input text-sm text-right" @input="calcLine(d)" /></td>
            <td class="td"><input v-model.number="d.descuento" type="number" step="0.01" min="0" class="input text-sm text-right" @input="calcLine(d)" /></td>
            <td class="td">
              <select v-model.number="d.impuesto_iva_id" @change="calcLine(d)" class="input text-sm">
                <option :value="null">0%</option>
                <option v-for="imp in impuestos" :key="imp.id" :value="imp.id">{{ imp.nombre }}</option>
              </select>
            </td>
            <td class="td text-right font-medium">${{ d._total?.toFixed(2) || '0.00' }}</td>
            <td class="td"><button @click="form.detalles.splice(i,1); calcTotals()" class="text-red-500 hover:text-red-700">✕</button></td>
          </tr>
        </tbody>
      </table>
      <button @click="addDetalle" class="btn-secondary">+ Agregar Línea</button>
    </div>
    <!-- Pagos -->
    <div class="bg-white rounded-xl shadow p-6 mb-4">
      <h3 class="font-semibold mb-3">Forma de Pago</h3>
      <table class="w-full mb-4">
        <thead class="bg-gray-50"><tr><th class="th">Forma de Pago</th><th class="th">Descripción</th><th class="th w-32">Monto</th><th class="th w-8"></th></tr></thead>
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
            <td class="td"><button @click="form.pagos.splice(i,1)" class="text-red-500 hover:text-red-700">✕</button></td>
          </tr>
        </tbody>
      </table>
      <button @click="form.pagos.push({forma_pago_id: null, descripcion: 'Pago', monto: totales.total})" class="btn-secondary">+ Agregar Pago</button>
    </div>
    <!-- Totales -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
      <div class="flex justify-end">
        <div class="w-64">
          <div v-if="totales.subtotal_15 > 0" class="flex justify-between py-1"><span>Subtotal 15%:</span><span>${{ totales.subtotal_15.toFixed(2) }}</span></div>
          <div v-if="totales.subtotal_5 > 0" class="flex justify-between py-1"><span>Subtotal 5%:</span><span>${{ totales.subtotal_5.toFixed(2) }}</span></div>
          <div v-if="totales.subtotal_0 > 0" class="flex justify-between py-1"><span>Subtotal 0%:</span><span>${{ totales.subtotal_0.toFixed(2) }}</span></div>
          <div v-if="totales.iva_15 > 0" class="flex justify-between py-1"><span>IVA 15%:</span><span>${{ totales.iva_15.toFixed(2) }}</span></div>
          <div v-if="totales.iva_5 > 0" class="flex justify-between py-1"><span>IVA 5%:</span><span>${{ totales.iva_5.toFixed(2) }}</span></div>
          <div class="flex justify-between py-2 border-t font-bold text-lg"><span>TOTAL:</span><span>${{ totales.total.toFixed(2) }}</span></div>
        </div>
      </div>
    </div>
    <div class="flex justify-end gap-3">
      <router-link to="/app/ventas" class="btn-secondary">Cancelar</router-link>
      <button @click="save" :disabled="saving" class="btn-primary">{{ saving ? 'Guardando...' : 'Guardar Factura' }}</button>
    </div>
  </div>
</template>
<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
const router = useRouter()
const clientes = ref([])
const productos = ref([])
const documentos = ref([])
const impuestos = ref([])
const formasPago = ref([])
const saving = ref(false)
const error = ref('')
const form = ref({
  empresa_id: auth.empresaActual?.id,
  cliente_id: null, documento_id: null,
  fecha: new Date().toISOString().slice(0,10), concepto: '',
  detalles: [],
  pagos: [{ forma_pago_id: null, descripcion: 'Pago contado', monto: 0 }]
})
const totales = reactive({ subtotal_15: 0, subtotal_5: 0, subtotal_0: 0, iva_15: 0, iva_5: 0, total: 0 })
onMounted(async () => {
  const [r1, r2, r3, r4, r5] = await Promise.all([
    api.get('/clientes', { params: { empresa_id: auth.empresaActual?.id } }),
    api.get('/productos', { params: { empresa_id: auth.empresaActual?.id } }),
    api.get('/documentos', { params: { empresa_id: auth.empresaActual?.id } }),
    api.get('/impuestos-iva', { params: { empresa_id: auth.empresaActual?.id } }),
    api.get('/formas-pago', { params: { empresa_id: auth.empresaActual?.id } }),
  ])
  clientes.value = r1.data; productos.value = r2.data; documentos.value = r3.data
  impuestos.value = r4.data; formasPago.value = r5.data
  addDetalle()
})
function addDetalle() {
  form.value.detalles.push({ producto_id: null, descripcion: '', cantidad: 1, precio_unitario: 0, descuento: 0, impuesto_iva_id: null, _total: 0 })
}
function onProductoChange(det) {
  if (!det.producto_id) return
  const p = productos.value.find(x => x.id === det.producto_id)
  if (p) { det.descripcion = p.nombre; det.precio_unitario = parseFloat(p.precio); det.impuesto_iva_id = p.impuesto_iva_id; calcLine(det) }
}
function calcLine(det) {
  const sub = (det.cantidad * det.precio_unitario) - (det.descuento || 0)
  const imp = impuestos.value.find(i => i.id === det.impuesto_iva_id)
  const iva = imp ? round2(sub * imp.porcentaje / 100) : 0
  det._total = round2(sub + iva)
  calcTotals()
}
function calcTotals() {
  totales.subtotal_15 = 0; totales.subtotal_5 = 0; totales.subtotal_0 = 0; totales.iva_15 = 0; totales.iva_5 = 0
  for (const det of form.value.detalles) {
    const sub = (det.cantidad * det.precio_unitario) - (det.descuento || 0)
    const imp = impuestos.value.find(i => i.id === det.impuesto_iva_id)
    const pct = imp?.porcentaje || 0
    const iva = round2(sub * pct / 100)
    if (pct === 15) { totales.subtotal_15 += sub; totales.iva_15 += iva }
    else if (pct === 5) { totales.subtotal_5 += sub; totales.iva_5 += iva }
    else { totales.subtotal_0 += sub }
  }
  totales.total = round2(totales.subtotal_15 + totales.subtotal_5 + totales.subtotal_0 + totales.iva_15 + totales.iva_5)
}
function round2(n) { return Math.round(n * 100) / 100 }
async function save() {
  error.value = ''; saving.value = true
  try {
    await api.post('/ventas', form.value)
    router.push('/app/ventas')
  } catch(e) { error.value = e.response?.data?.message || 'Error al guardar' }
  finally { saving.value = false }
}
</script>
