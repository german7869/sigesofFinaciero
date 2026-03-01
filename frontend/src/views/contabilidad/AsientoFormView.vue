<template>
  <div class="p-6">
    <div class="flex items-center gap-4 mb-6">
      <router-link to="/app/asientos" class="text-blue-600 hover:underline">← Volver</router-link>
      <h2 class="text-2xl font-bold text-gray-800">Nuevo Asiento Contable</h2>
    </div>
    <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">{{ error }}</div>
    <div class="bg-white rounded-xl shadow p-6">
      <div class="grid grid-cols-3 gap-4 mb-6">
        <div><label class="label">Fecha *</label><input v-model="form.fecha" type="date" required class="input" /></div>
        <div><label class="label">Concepto *</label><input v-model="form.concepto" required class="input" /></div>
        <div><label class="label">Beneficiario</label><input v-model="form.beneficiario" class="input" /></div>
      </div>
      <h3 class="font-semibold mb-3">Líneas del Asiento</h3>
      <table class="w-full mb-4">
        <thead class="bg-gray-50"><tr>
          <th class="th">Cuenta</th><th class="th">Descripción</th><th class="th w-32">Debe</th><th class="th w-32">Haber</th><th class="th w-10"></th>
        </tr></thead>
        <tbody>
          <tr v-for="(d, i) in form.detalles" :key="i">
            <td class="td">
              <select v-model.number="d.cuenta_contable_id" class="input text-sm">
                <option v-for="c in cuentas" :key="c.id" :value="c.id">{{ c.codigo }} - {{ c.descripcion }}</option>
              </select>
            </td>
            <td class="td"><input v-model="d.descripcion" class="input text-sm" /></td>
            <td class="td"><input v-model.number="d.debe" type="number" step="0.01" class="input text-sm text-right" /></td>
            <td class="td"><input v-model.number="d.haber" type="number" step="0.01" class="input text-sm text-right" /></td>
            <td class="td"><button @click="form.detalles.splice(i,1)" class="text-red-500 hover:text-red-700">✕</button></td>
          </tr>
        </tbody>
        <tfoot class="bg-gray-50 font-semibold">
          <tr>
            <td colspan="2" class="td text-right">TOTALES:</td>
            <td class="td text-right" :class="totalDebe !== totalHaber ? 'text-red-600' : 'text-green-600'">{{ totalDebe.toFixed(2) }}</td>
            <td class="td text-right" :class="totalDebe !== totalHaber ? 'text-red-600' : 'text-green-600'">{{ totalHaber.toFixed(2) }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
      <button @click="addLinea" class="btn-secondary mb-6">+ Agregar Línea</button>
      <div class="flex justify-end gap-3">
        <router-link to="/app/asientos" class="btn-secondary">Cancelar</router-link>
        <button @click="save" :disabled="saving" class="btn-primary">{{ saving ? 'Guardando...' : 'Guardar Asiento' }}</button>
      </div>
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
const cuentas = ref([])
const saving = ref(false)
const error = ref('')
const form = ref({
  empresa_id: auth.empresaActual?.id,
  fecha: new Date().toISOString().slice(0,10),
  concepto: '', beneficiario: '',
  detalles: [{ cuenta_contable_id: null, descripcion: '', debe: 0, haber: 0 }, { cuenta_contable_id: null, descripcion: '', debe: 0, haber: 0 }]
})
onMounted(async () => { const r = await api.get('/cuentas-contables/flat', { params: { empresa_id: auth.empresaActual?.id } }); cuentas.value = r.data })
const totalDebe = computed(() => form.value.detalles.reduce((s,d) => s + (parseFloat(d.debe)||0), 0))
const totalHaber = computed(() => form.value.detalles.reduce((s,d) => s + (parseFloat(d.haber)||0), 0))
function addLinea() { form.value.detalles.push({ cuenta_contable_id: null, descripcion: '', debe: 0, haber: 0 }) }
async function save() {
  error.value = ''
  if (Math.abs(totalDebe.value - totalHaber.value) > 0.01) { error.value = 'El Debe y el Haber deben ser iguales'; return }
  saving.value = true
  try {
    await api.post('/asientos', form.value)
    router.push('/app/asientos')
  } catch(e) { error.value = e.response?.data?.message || 'Error al guardar' }
  finally { saving.value = false }
}
</script>
