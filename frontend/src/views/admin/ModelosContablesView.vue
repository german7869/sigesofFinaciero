<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Modelos Contables</h2>
      <button @click="showForm=true; form={empresa_id: auth.empresaActual?.id}" class="btn-primary">+ Nuevo</button>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50"><tr><th class="th">Módulo</th><th class="th">Campo</th><th class="th">Cuenta</th><th class="th">Acciones</th></tr></thead>
        <tbody>
          <tr v-for="item in items" :key="item.id" class="border-t hover:bg-gray-50">
            <td class="td">{{ item.modulo }}</td><td class="td">{{ item.campo }}</td>
            <td class="td">{{ item.cuenta_contable?.codigo }} - {{ item.cuenta_contable?.descripcion }}</td>
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
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Editar' : 'Nuevo' }} Modelo Contable</h3>
        <form @submit.prevent="save">
          <div class="grid grid-cols-2 gap-3">
            <div><label class="label">Módulo *</label>
              <select v-model="form.modulo" required class="input">
                <option>VENTAS</option><option>COMPRAS</option>
              </select>
            </div>
            <div><label class="label">Campo *</label>
              <input v-model="form.campo" required class="input" placeholder="SUBTOTAL_15, IVA_15, CREDITO..." />
            </div>
            <div class="col-span-2"><label class="label">Cuenta Contable *</label>
              <select v-model.number="form.cuenta_contable_id" required class="input">
                <option v-for="c in cuentas" :key="c.id" :value="c.id">{{ c.codigo }} - {{ c.descripcion }}</option>
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
const items = ref([])
const cuentas = ref([])
const showForm = ref(false)
const form = ref({})
onMounted(async () => {
  const [r1, r2] = await Promise.all([
    api.get('/modelos-contables', { params: { empresa_id: auth.empresaActual?.id } }),
    api.get('/cuentas-contables/flat', { params: { empresa_id: auth.empresaActual?.id } })
  ])
  items.value = r1.data; cuentas.value = r2.data
})
function editItem(i) { form.value = { ...i }; showForm.value = true }
async function save() {
  if (form.value.id) { await api.put(`/modelos-contables/${form.value.id}`, form.value) }
  else { await api.post('/modelos-contables', form.value) }
  showForm.value = false
  const r = await api.get('/modelos-contables', { params: { empresa_id: auth.empresaActual?.id } }); items.value = r.data
}
async function deleteItem(id) {
  if (!confirm('¿Eliminar?')) return
  await api.delete(`/modelos-contables/${id}`)
  items.value = items.value.filter(i => i.id !== id)
}
</script>
