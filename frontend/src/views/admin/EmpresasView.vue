<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Empresas</h2>
      <button @click="showForm=true; form={}" class="btn-primary">+ Nueva Empresa</button>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr>
          <th class="th">RUC</th><th class="th">Razón Social</th><th class="th">Ciudad</th><th class="th">Email</th><th class="th">Acciones</th>
        </tr></thead>
        <tbody>
          <tr v-for="e in empresas" :key="e.id" class="border-t hover:bg-gray-50">
            <td class="td">{{ e.ruc }}</td><td class="td">{{ e.razon_social }}</td>
            <td class="td">{{ e.ciudad }}</td><td class="td">{{ e.email }}</td>
            <td class="td">
              <button @click="editItem(e)" class="text-blue-600 hover:underline mr-3">Editar</button>
              <button @click="deleteItem(e.id)" class="text-red-600 hover:underline">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Modal -->
    <div v-if="showForm" class="modal-overlay" @click.self="showForm=false">
      <div class="modal-box">
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Editar' : 'Nueva' }} Empresa</h3>
        <form @submit.prevent="save">
          <div class="grid grid-cols-2 gap-3">
            <div><label class="label">RUC *</label><input v-model="form.ruc" required class="input" /></div>
            <div><label class="label">Razón Social *</label><input v-model="form.razon_social" required class="input" /></div>
            <div><label class="label">Nombre Comercial</label><input v-model="form.nombre_comercial" class="input" /></div>
            <div><label class="label">Dirección</label><input v-model="form.direccion" class="input" /></div>
            <div><label class="label">Ciudad</label><input v-model="form.ciudad" class="input" /></div>
            <div><label class="label">Email</label><input v-model="form.email" type="email" class="input" /></div>
            <div><label class="label">Teléfono</label><input v-model="form.telefono" class="input" /></div>
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
const empresas = ref([])
const showForm = ref(false)
const form = ref({})
onMounted(async () => { const r = await api.get('/empresas'); empresas.value = r.data })
function editItem(e) { form.value = { ...e }; showForm.value = true }
async function save() {
  if (form.value.id) { await api.put(`/empresas/${form.value.id}`, form.value) }
  else { await api.post('/empresas', form.value) }
  showForm.value = false
  const r = await api.get('/empresas'); empresas.value = r.data
}
async function deleteItem(id) {
  if (!confirm('¿Eliminar?')) return
  await api.delete(`/empresas/${id}`)
  empresas.value = empresas.value.filter(e => e.id !== id)
}
</script>
