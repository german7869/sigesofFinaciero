<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Usuarios</h2>
      <button @click="showForm=true; form={}" class="btn-primary">+ Nuevo Usuario</button>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr>
          <th class="th">Nombre</th><th class="th">Correo</th><th class="th">Rol</th><th class="th">Empresas</th><th class="th">Acciones</th>
        </tr></thead>
        <tbody>
          <tr v-for="u in usuarios" :key="u.id" class="border-t hover:bg-gray-50">
            <td class="td">{{ u.nombres }}</td><td class="td">{{ u.correo }}</td>
            <td class="td capitalize">{{ u.rol }}</td>
            <td class="td">{{ u.empresas?.map(e=>e.nombre_comercial||e.razon_social).join(', ') }}</td>
            <td class="td">
              <button @click="editItem(u)" class="text-blue-600 hover:underline mr-3">Editar</button>
              <button @click="deleteItem(u.id)" class="text-red-600 hover:underline">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showForm" class="modal-overlay" @click.self="showForm=false">
      <div class="modal-box">
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Editar' : 'Nuevo' }} Usuario</h3>
        <form @submit.prevent="save">
          <div class="grid grid-cols-2 gap-3">
            <div><label class="label">Nombres *</label><input v-model="form.nombres" required class="input" /></div>
            <div><label class="label">Correo *</label><input v-model="form.correo" type="email" required class="input" /></div>
            <div><label class="label">Contraseña {{ form.id ? '(dejar vacío para no cambiar)' : '*' }}</label><input v-model="form.password" type="password" :required="!form.id" class="input" /></div>
            <div><label class="label">Rol *</label>
              <select v-model="form.rol" required class="input">
                <option value="admin">Admin</option><option value="contador">Contador</option>
                <option value="auxiliar">Auxiliar</option><option value="cajero">Cajero</option>
              </select>
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
const usuarios = ref([])
const showForm = ref(false)
const form = ref({})
onMounted(async () => { const r = await api.get('/usuarios'); usuarios.value = r.data })
function editItem(u) { form.value = { ...u, password: '' }; showForm.value = true }
async function save() {
  if (form.value.id) { await api.put(`/usuarios/${form.value.id}`, form.value) }
  else { await api.post('/usuarios', form.value) }
  showForm.value = false
  const r = await api.get('/usuarios'); usuarios.value = r.data
}
async function deleteItem(id) {
  if (!confirm('¿Eliminar?')) return
  await api.delete(`/usuarios/${id}`)
  usuarios.value = usuarios.value.filter(u => u.id !== id)
}
</script>
