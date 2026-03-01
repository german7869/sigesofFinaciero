<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Documentos</h2>
      <button @click="showForm=true; form={empresa_id: auth.empresaActual?.id}" class="btn-primary">+ Nuevo</button>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr>
          <th class="th">Código</th><th class="th">Descripción</th><th class="th">Módulo</th><th class="th">Establec.</th><th class="th">P.Emisión</th><th class="th">Secuencia</th><th class="th">Acciones</th>
        </tr></thead>
        <tbody>
          <tr v-for="d in docs" :key="d.id" class="border-t hover:bg-gray-50">
            <td class="td">{{ d.codigo }}</td><td class="td">{{ d.descripcion }}</td><td class="td">{{ d.modulo }}</td>
            <td class="td">{{ d.cod_establecimiento }}</td><td class="td">{{ d.punto_emision }}</td><td class="td">{{ d.secuencia_actual }}</td>
            <td class="td">
              <button @click="editItem(d)" class="text-blue-600 hover:underline mr-3">Editar</button>
              <button @click="deleteItem(d.id)" class="text-red-600 hover:underline">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showForm" class="modal-overlay" @click.self="showForm=false">
      <div class="modal-box">
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Editar' : 'Nuevo' }} Documento</h3>
        <form @submit.prevent="save">
          <div class="grid grid-cols-2 gap-3">
            <div><label class="label">Código *</label><input v-model="form.codigo" required class="input" /></div>
            <div><label class="label">Descripción *</label><input v-model="form.descripcion" required class="input" /></div>
            <div><label class="label">Módulo *</label>
              <select v-model="form.modulo" required class="input">
                <option>VENTAS</option><option>COMPRAS</option><option>CONTABILIDAD</option>
              </select>
            </div>
            <div><label class="label">Establecimiento</label><input v-model="form.cod_establecimiento" class="input" /></div>
            <div><label class="label">Punto Emisión</label><input v-model="form.punto_emision" class="input" /></div>
            <div><label class="label">Secuencia Actual</label><input v-model.number="form.secuencia_actual" type="number" class="input" /></div>
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
const docs = ref([])
const showForm = ref(false)
const form = ref({})
onMounted(async () => { const r = await api.get('/documentos', { params: { empresa_id: auth.empresaActual?.id } }); docs.value = r.data })
function editItem(d) { form.value = { ...d }; showForm.value = true }
async function save() {
  if (form.value.id) { await api.put(`/documentos/${form.value.id}`, form.value) }
  else { await api.post('/documentos', form.value) }
  showForm.value = false
  const r = await api.get('/documentos', { params: { empresa_id: auth.empresaActual?.id } }); docs.value = r.data
}
async function deleteItem(id) {
  if (!confirm('¿Eliminar?')) return
  await api.delete(`/documentos/${id}`)
  docs.value = docs.value.filter(d => d.id !== id)
}
</script>
