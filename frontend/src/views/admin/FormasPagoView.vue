<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Formas de Pago</h2>
      <button @click="showForm=true; form={empresa_id: auth.empresaActual?.id}" class="btn-primary">+ Nueva</button>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr><th class="th">Código</th><th class="th">Nombre</th><th class="th">Acciones</th></tr></thead>
        <tbody>
          <tr v-for="fp in items" :key="fp.id" class="border-t hover:bg-gray-50">
            <td class="td">{{ fp.codigo }}</td><td class="td">{{ fp.nombre }}</td>
            <td class="td">
              <button @click="editItem(fp)" class="text-blue-600 hover:underline mr-3">Editar</button>
              <button @click="deleteItem(fp.id)" class="text-red-600 hover:underline">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showForm" class="modal-overlay" @click.self="showForm=false">
      <div class="modal-box">
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Editar' : 'Nueva' }} Forma de Pago</h3>
        <form @submit.prevent="save">
          <div><label class="label">Código *</label><input v-model="form.codigo" required class="input mb-3" /></div>
          <div><label class="label">Nombre *</label><input v-model="form.nombre" required class="input mb-3" /></div>
          <div><label class="label">Cuenta Contable ID</label><input v-model.number="form.cuenta_contable_id" type="number" class="input mb-3" /></div>
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
const items = ref([])
const showForm = ref(false)
const form = ref({})
onMounted(async () => { const r = await api.get('/formas-pago', { params: { empresa_id: auth.empresaActual?.id } }); items.value = r.data })
function editItem(i) { form.value = { ...i }; showForm.value = true }
async function save() {
  if (form.value.id) { await api.put(`/formas-pago/${form.value.id}`, form.value) }
  else { await api.post('/formas-pago', form.value) }
  showForm.value = false
  const r = await api.get('/formas-pago', { params: { empresa_id: auth.empresaActual?.id } }); items.value = r.data
}
async function deleteItem(id) {
  if (!confirm('¿Eliminar?')) return
  await api.delete(`/formas-pago/${id}`)
  items.value = items.value.filter(i => i.id !== id)
}
</script>
