<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Productos</h2>
      <button @click="showForm=true; form={empresa_id: auth.empresaActual?.id}" class="btn-primary">+ Nuevo Producto</button>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr>
          <th class="th">Código</th><th class="th">Nombre</th><th class="th">Categoría</th><th class="th">Precio</th><th class="th">IVA</th><th class="th">Acciones</th>
        </tr></thead>
        <tbody>
          <tr v-for="p in productos" :key="p.id" class="border-t hover:bg-gray-50">
            <td class="td">{{ p.codigo }}</td><td class="td">{{ p.nombre }}</td>
            <td class="td">{{ p.categoria }}</td><td class="td">${{ parseFloat(p.precio).toFixed(2) }}</td>
            <td class="td">{{ p.impuesto_iva?.nombre }}</td>
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
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Editar' : 'Nuevo' }} Producto</h3>
        <form @submit.prevent="save">
          <div class="grid grid-cols-2 gap-3">
            <div><label class="label">Código *</label><input v-model="form.codigo" required class="input" /></div>
            <div><label class="label">Nombre *</label><input v-model="form.nombre" required class="input" /></div>
            <div><label class="label">Categoría</label><input v-model="form.categoria" class="input" /></div>
            <div><label class="label">Precio *</label><input v-model.number="form.precio" required type="number" step="0.01" class="input" /></div>
            <div><label class="label">Impuesto IVA</label>
              <select v-model.number="form.impuesto_iva_id" class="input">
                <option :value="null">Sin IVA</option>
                <option v-for="imp in impuestos" :key="imp.id" :value="imp.id">{{ imp.nombre }} ({{ imp.porcentaje }}%)</option>
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
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
const productos = ref([])
const impuestos = ref([])
const showForm = ref(false)
const form = ref({})
onMounted(async () => {
  const [r1, r2] = await Promise.all([
    api.get('/productos', { params: { empresa_id: auth.empresaActual?.id } }),
    api.get('/impuestos-iva', { params: { empresa_id: auth.empresaActual?.id } })
  ])
  productos.value = r1.data; impuestos.value = r2.data
})
function editItem(p) { form.value = { ...p }; showForm.value = true }
async function save() {
  if (form.value.id) { await api.put(`/productos/${form.value.id}`, form.value) }
  else { await api.post('/productos', form.value) }
  showForm.value = false
  const r = await api.get('/productos', { params: { empresa_id: auth.empresaActual?.id } }); productos.value = r.data
}
async function deleteItem(id) {
  if (!confirm('¿Eliminar?')) return
  await api.delete(`/productos/${id}`)
  productos.value = productos.value.filter(p => p.id !== id)
}
</script>
