<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Proveedores</h2>
      <button @click="showForm=true; form={empresa_id: auth.empresaActual?.id}" class="btn-primary">+ Nuevo Proveedor</button>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr><th class="th">Identificación</th><th class="th">Nombre</th><th class="th">Teléfono</th><th class="th">Correo</th><th class="th">Acciones</th></tr></thead>
        <tbody>
          <tr v-for="p in proveedores" :key="p.id" class="border-t hover:bg-gray-50">
            <td class="td">{{ p.identificacion }}</td><td class="td">{{ p.nombres }}</td>
            <td class="td">{{ p.telefono }}</td><td class="td">{{ p.correo }}</td>
            <td class="td">
              <button @click="editItem(p)" class="text-blue-600 hover:underline mr-3">Editar</button>
              <button @click="deleteItem(p.id)" class="text-red-600 hover:underline">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showForm" class="modal-overlay" @click.self="showForm=false">
      <div class="modal-box">
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Editar' : 'Nuevo' }} Proveedor</h3>
        <form @submit.prevent="save">
          <div class="grid grid-cols-2 gap-3">
            <div><label class="label">Identificación *</label><input v-model="form.identificacion" required class="input" /></div>
            <div><label class="label">Nombres *</label><input v-model="form.nombres" required class="input" /></div>
            <div><label class="label">Dirección</label><input v-model="form.direccion" class="input" /></div>
            <div><label class="label">Teléfono</label><input v-model="form.telefono" class="input" /></div>
            <div><label class="label">Correo</label><input v-model="form.correo" type="email" class="input" /></div>
            <div><label class="label">Ciudad</label><input v-model="form.ciudad" class="input" /></div>
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
const proveedores = ref([])
const showForm = ref(false)
const form = ref({})
onMounted(async () => { const r = await api.get('/proveedores', { params: { empresa_id: auth.empresaActual?.id } }); proveedores.value = r.data })
function editItem(p) { form.value = { ...p }; showForm.value = true }
async function save() {
  if (form.value.id) { await api.put(`/proveedores/${form.value.id}`, form.value) }
  else { await api.post('/proveedores', form.value) }
  showForm.value = false
  const r = await api.get('/proveedores', { params: { empresa_id: auth.empresaActual?.id } }); proveedores.value = r.data
}
async function deleteItem(id) {
  if (!confirm('¿Eliminar?')) return
  await api.delete(`/proveedores/${id}`)
  proveedores.value = proveedores.value.filter(p => p.id !== id)
}
</script>
