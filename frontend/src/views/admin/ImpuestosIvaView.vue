<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Impuestos IVA</h2>
      <button @click="showForm=true; form={empresa_id: auth.empresaActual?.id}" class="btn-primary">+ Nuevo</button>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr><th class="th">Código</th><th class="th">Nombre</th><th class="th">Porcentaje</th><th class="th">Acciones</th></tr></thead>
        <tbody>
          <tr v-for="item in items" :key="item.id" class="border-t hover:bg-gray-50">
            <td class="td">{{ item.codigo }}</td><td class="td">{{ item.nombre }}</td><td class="td">{{ item.porcentaje }}%</td>
            <td class="td">
              <button @click="editItem(item)" class="text-blue-600 hover:underline mr-3">Editar</button>
              <button @click="deleteItem(item.id)" class="text-red-600 hover:underline">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showForm" class="modal-overlay" @click.self="showForm=false">
      <div class="modal-box">
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Editar' : 'Nuevo' }} Impuesto IVA</h3>
        <form @submit.prevent="save">
          <div class="grid grid-cols-2 gap-3">
            <div><label class="label">Código *</label><input v-model="form.codigo" required class="input" /></div>
            <div><label class="label">Nombre *</label><input v-model="form.nombre" required class="input" /></div>
            <div><label class="label">Porcentaje *</label><input v-model.number="form.porcentaje" required type="number" step="0.01" class="input" /></div>
            <div><label class="label">Cuenta Contable ID</label><input v-model.number="form.cuenta_contable_id" type="number" class="input" /></div>
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
const items = ref([])
const showForm = ref(false)
const form = ref({})
onMounted(async () => { const r = await api.get('/impuestos-iva', { params: { empresa_id: auth.empresaActual?.id } }); items.value = r.data })
function editItem(i) { form.value = { ...i }; showForm.value = true }
async function save() {
  if (form.value.id) { await api.put(`/impuestos-iva/${form.value.id}`, form.value) }
  else { await api.post('/impuestos-iva', form.value) }
  showForm.value = false
  const r = await api.get('/impuestos-iva', { params: { empresa_id: auth.empresaActual?.id } }); items.value = r.data
}
async function deleteItem(id) {
  if (!confirm('¿Eliminar?')) return
  await api.delete(`/impuestos-iva/${id}`)
  items.value = items.value.filter(i => i.id !== id)
}
</script>
