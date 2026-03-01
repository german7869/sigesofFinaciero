<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Permisos de Módulos</h2>
    <div class="bg-white rounded-xl shadow p-6">
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
          <label class="label">Usuario</label>
          <select v-model="selectedUser" @change="loadPermisos" class="input">
            <option value="">-- Seleccionar --</option>
            <option v-for="u in usuarios" :key="u.id" :value="u.id">{{ u.nombres }}</option>
          </select>
        </div>
        <div>
          <label class="label">Empresa</label>
          <select v-model="selectedEmpresa" @change="loadPermisos" class="input">
            <option value="">-- Seleccionar --</option>
            <option v-for="e in empresas" :key="e.id" :value="e.id">{{ e.razon_social }}</option>
          </select>
        </div>
      </div>
      <div v-if="selectedUser && selectedEmpresa">
        <h3 class="font-semibold mb-3">Módulos</h3>
        <div class="grid grid-cols-3 gap-3 mb-6">
          <label v-for="m in modulos" :key="m" class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" :value="m" v-model="selectedModulos" class="rounded" />
            <span>{{ m }}</span>
          </label>
        </div>
        <button @click="save" class="btn-primary">Guardar Permisos</button>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'
const usuarios = ref([])
const empresas = ref([])
const selectedUser = ref('')
const selectedEmpresa = ref('')
const selectedModulos = ref([])
const modulos = ['VENTAS', 'COMPRAS', 'CONTABILIDAD', 'BANCOS', 'SRI', 'REPORTES']
onMounted(async () => {
  const [r1, r2] = await Promise.all([api.get('/usuarios'), api.get('/empresas')])
  usuarios.value = r1.data; empresas.value = r2.data
})
async function loadPermisos() {
  if (!selectedUser.value || !selectedEmpresa.value) return
  const r = await api.get('/permisos', { params: { user_id: selectedUser.value, empresa_id: selectedEmpresa.value } })
  selectedModulos.value = r.data.map(p => p.modulo)
}
async function save() {
  await api.post('/permisos/sync', { user_id: selectedUser.value, empresa_id: selectedEmpresa.value, modulos: selectedModulos.value })
  alert('Permisos guardados')
}
</script>
