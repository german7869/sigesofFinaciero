<template>
  <div class="p-6">
    <div class="flex items-center gap-4 mb-6">
      <router-link to="/app/compras" class="text-blue-600 hover:underline">← Volver</router-link>
      <h2 class="text-2xl font-bold text-gray-800">Nueva Factura de Compra</h2>
    </div>
    <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">{{ error }}</div>
    <div class="bg-white rounded-xl shadow p-6 mb-4">
      <div class="grid grid-cols-3 gap-4">
        <div><label class="label">Proveedor *</label>
          <select v-model.number="form.proveedor_id" required class="input">
            <option v-for="p in proveedores" :key="p.id" :value="p.id">{{ p.identificacion }} - {{ p.nombres }}</option>
          </select>
        </div>
        <div><label class="label">Número *</label><input v-model="form.numero" required class="input" /></div>
        <div><label class="label">Fecha *</label><input v-model="form.fecha" type="date" required class="input" /></div>
        <div class="col-span-3"><label class="label">Concepto</label><input v-model="form.concepto" class="input" /></div>
      </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 mb-4">
      <h3 class="font-semibold mb-3">Detalle</h3>
      <table class="w-full mb-4">
        <thead class="bg-gray-50"><tr>
          <th class="th">Descripción</th><th class="th w-20">Cant.</th><th class="th w-28">Precio</th><th class="th">IVA</th><th class="th w-28">Total</th><th class="th w-8"></th>
        </tr></thead>
        <tbody>
          <tr v-for="(d, i) in form.detalles" :key="i">
            <td class="td"><input v-model="d.descripcion" required class="input text-sm" /></td>
            <td class="td"><input v-model.number="d.cantidad" type="number" step="0.01" class="input text-sm text-right" @input="calcLine(d)" /></td>
            <td class="td"><input v-model.number="d.precio_unitario" type="number" step="0.01" class="input text-sm text-right" @input="calcLine(d)" /></td>
            <td class="td">
              <select v-model.number="d.impuesto_iva_id" @change="calcLine(d)" class="input text-sm">
                <option :value="null">0%</option>
                <option v-for="imp in impuestos" :key="imp.id" :value="imp.id">{{ imp.nombre }}</option>
              </select>
            </td>
            <td class="td text-right font-medium">${{ d._total?.toFixed(2) || '0.00' }}</td>
            <td class="td"><button @click="form.detalles.splice(i,1)" class="text-red-500">✕</button></td>
          </tr>
        </tbody>
      </table>
      <button @click="form.detalles.push({descripcion:'',cantidad:1,precio_unitario:0,impuesto_iva_id:null,_total:0})" class="btn-secondary">+ Agregar Línea</button>
    </div>
    <!-- Retenciones -->
    <div class="bg-white rounded-xl shadow p-6 mb-4">
      <h3 class="font-semibold mb-3">Retenciones</h3>
      <table class="w-full mb-4">
        <thead class="bg-gray-50"><tr><th class="th">Tipo</th><th class="th">%</th><th class="th">Base</th><th class="th">Valor</th><th class="th w-8"></th></tr></thead>
        <tbody>
          <tr v-for="(r, i) in form.retenciones" :key="i">
            <td class="td"><input v-model="r.tipo" placeholder="RENTA, IVA" required class="input text-sm" /></td>
            <td class="td"><input v-model.number="r.porcentaje" type="number" step="0.01" class="input text-sm text-right" @input="calcRetencion(r)" /></td>
            <td class="td"><input v-model.number="r.base" type="number" step="0.01" class="input text-sm text-right" @input="calcRetencion(r)" /></td>
            <td class="td text-right">${{ r.valor?.toFixed(2) || '0.00' }}</td>
            <td class="td"><button @click="form.retenciones.splice(i,1)" class="text-red-500">✕</button></td>
          </tr>
        </tbody>
      </table>
      <button @click="form.retenciones.push({tipo:'',porcentaje:0,base:0,valor:0})" class="btn-secondary">+ Agregar Retención</button>
    </div>
    <!-- Pagos -->
    <div class="bg-white rounded-xl shadow p-6 mb-4">
      <h3 class="font-semibold mb-3">Formas de Pago</h3>
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
      <button @click="form.pagos.push({forma_pago_id: null, descripcion: 'Pago', monto: 0})" class="btn-secondary">+ Agregar Pago</button>
    </div>
    <div class="flex justify-end gap-3">
      <router-link to="/app/compras" class="btn-secondary">Cancelar</router-link>
      <button @click="save" :disabled="saving" class="btn-primary">{{ saving ? 'Guardando...' : 'Guardar Compra' }}</button>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
const router = useRouter()
const proveedores = ref([])
const impuestos = ref([])
const formasPago = ref([])
const saving = ref(false)
const error = ref('')
const form = ref({
  empresa_id: auth.empresaActual?.id,
  proveedor_id: null, numero: '', fecha: new Date().toISOString().slice(0,10), concepto: '',
  detalles: [{ descripcion: '', cantidad: 1, precio_unitario: 0, impuesto_iva_id: null, _total: 0 }],
  retenciones: [], pagos: [{ forma_pago_id: null, descripcion: 'Pago', monto: 0 }]
})
onMounted(async () => {
  const [r1, r2, r3] = await Promise.all([
    api.get('/proveedores', { params: { empresa_id: auth.empresaActual?.id } }),
    api.get('/impuestos-iva', { params: { empresa_id: auth.empresaActual?.id } }),
    api.get('/formas-pago', { params: { empresa_id: auth.empresaActual?.id } })
  ])
  proveedores.value = r1.data; impuestos.value = r2.data; formasPago.value = r3.data
})
function calcLine(det) {
  const sub = det.cantidad * det.precio_unitario
  const imp = impuestos.value.find(i => i.id === det.impuesto_iva_id)
  const iva = imp ? Math.round(sub * imp.porcentaje) / 100 : 0
  det._total = Math.round((sub + iva) * 100) / 100
}
function calcRetencion(r) { r.valor = Math.round(r.base * r.porcentaje) / 100 }
async function save() {
  error.value = ''; saving.value = true
  try {
    await api.post('/compras', form.value)
    router.push('/app/compras')
  } catch(e) { error.value = e.response?.data?.message || 'Error al guardar' }
  finally { saving.value = false }
}
</script>
