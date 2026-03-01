<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Plan de Cuentas</h2>
      <button @click="showForm=true; form={empresa_id: auth.empresaActual?.id}" class="btn-primary">+ Nueva Cuenta</button>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr>
          <th class="th">Código</th><th class="th">Descripción</th><th class="th">Tipo</th><th class="th">Nivel</th><th class="th">Auxiliar</th><th class="th">Acciones</th>
        </tr></thead>
        <tbody>
          <tr v-for="c in cuentasFlat" :key="c.id" class="border-t hover:bg-gray-50">
            <td class="td font-mono">{{ c.codigo }}</td>
            <td class="td" :style="{paddingLeft: (c.nivel * 12) + 'px'}">{{ c.descripcion }}</td>
            <td class="td"><span class="text-xs px-2 py-1 rounded" :class="tipoColor(c.tipo)">{{ c.tipo }}</span></td>
            <td class="td">{{ c.nivel }}</td>
            <td class="td">{{ c.es_auxiliar ? '✓' : '' }}</td>
            <td class="td">
              <button @click="editItem(c)" class="text-blue-600 hover:underline mr-3">Editar</button>
              <button @click="deleteItem(c.id)" class="text-red-600 hover:underline">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showForm" class="modal-overlay" @click.self="showForm=false">
      <div class="modal-box">
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Editar' : 'Nueva' }} Cuenta</h3>
        <form @submit.prevent="save">
          <div class="grid grid-cols-2 gap-3">
            <div><label class="label">Código *</label><input v-model="form.codigo" required class="input" /></div>
            <div><label class="label">Descripción *</label><input v-model="form.descripcion" required class="input" /></div>
            <div><label class="label">Nivel *</label><input v-model.number="form.nivel" required type="number" min="1" class="input" /></div>
            <div><label class="label">Tipo *</label>
              <select v-model="form.tipo" required class="input">
                <option v-for="t in tipos" :key="t" :value="t">{{ t }}</option>
              </select>
            </div>
            <div><label class="label">Cuenta Padre</label>
              <select v-model.number="form.parent_id" class="input">
                <option :value="null">-- Ninguna --</option>
                <option v-for="c in cuentasFlat" :key="c.id" :value="c.id">{{ c.codigo }} - {{ c.descripcion }}</option>
              </select>
            </div>
            <div class="flex items-center gap-2 mt-6">
              <input type="checkbox" v-model="form.es_auxiliar" id="esAux" class="rounded" />
              <label for="esAux" class="label">Es Auxiliar</label>
            </div>
          </div>
          <div class="flex justify-end gap-3 mt-4">
            <button type="button" @click="showForm=false" class="btn-secondary">Cancelar</button>
            <button type="submit" class="btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
const cuentasFlat = ref([])
const showForm = ref(false)
const form = ref({})
const tipos = ['ACTIVO', 'PASIVO', 'PATRIMONIO', 'INGRESO', 'GASTO', 'COSTO']
const tipoColor = (t) => ({ ACTIVO:'bg-green-100 text-green-800', PASIVO:'bg-red-100 text-red-800', PATRIMONIO:'bg-purple-100 text-purple-800', INGRESO:'bg-blue-100 text-blue-800', GASTO:'bg-orange-100 text-orange-800', COSTO:'bg-yellow-100 text-yellow-800' }[t] || '')
onMounted(async () => { await loadCuentas() })
async function loadCuentas() { const r = await api.get('/cuentas-contables/flat', { params: { empresa_id: auth.empresaActual?.id } }); cuentasFlat.value = r.data }
function editItem(c) { form.value = { ...c }; showForm.value = true }
async function save() {
  if (form.value.id) { await api.put(`/cuentas-contables/${form.value.id}`, form.value) }
  else { await api.post('/cuentas-contables', form.value) }
  showForm.value = false; await loadCuentas()
}
async function deleteItem(id) {
  if (!confirm('¿Eliminar?')) return
  await api.delete(`/cuentas-contables/${id}`)
  await loadCuentas()
}
</script>
