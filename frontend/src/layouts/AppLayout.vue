<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col">
      <div class="p-4 border-b border-blue-800">
        <h1 class="text-lg font-bold">SIGE Soft</h1>
        <p class="text-xs text-blue-300">Financiero</p>
      </div>

      <!-- Empresa selector -->
      <div class="p-3 border-b border-blue-800">
        <select v-if="auth.user?.empresas?.length" @change="cambiarEmpresa" :value="auth.empresaActual?.id" class="w-full bg-blue-800 text-white text-sm rounded px-2 py-1 border border-blue-700">
          <option v-for="e in auth.user.empresas" :key="e.id" :value="e.id">{{ e.nombre_comercial || e.razon_social }}</option>
        </select>
        <p v-else class="text-blue-300 text-xs">Sin empresa asignada</p>
      </div>

      <nav class="flex-1 overflow-y-auto p-2">
        <router-link to="/app/dashboard" class="nav-item" active-class="nav-active">
          📊 Dashboard
        </router-link>

        <div class="mt-3">
          <p class="text-blue-400 text-xs uppercase px-3 mb-1 font-semibold">Administración</p>
          <router-link v-if="auth.isAdmin" to="/app/empresas" class="nav-item" active-class="nav-active">🏢 Empresas</router-link>
          <router-link v-if="auth.isAdmin" to="/app/usuarios" class="nav-item" active-class="nav-active">👤 Usuarios</router-link>
          <router-link v-if="auth.isAdmin" to="/app/documentos" class="nav-item" active-class="nav-active">📄 Documentos</router-link>
          <router-link v-if="auth.isAdmin" to="/app/formas-pago" class="nav-item" active-class="nav-active">💳 Formas de Pago</router-link>
          <router-link v-if="auth.isAdmin" to="/app/impuestos-iva" class="nav-item" active-class="nav-active">% Impuestos IVA</router-link>
          <router-link v-if="auth.isAdmin" to="/app/modelos-contables" class="nav-item" active-class="nav-active">📋 Modelos Contables</router-link>
          <router-link v-if="auth.isAdmin" to="/app/permisos" class="nav-item" active-class="nav-active">🔒 Permisos</router-link>
        </div>

        <div class="mt-3">
          <p class="text-blue-400 text-xs uppercase px-3 mb-1 font-semibold">Contabilidad</p>
          <router-link to="/app/cuentas-contables" class="nav-item" active-class="nav-active">📒 Plan de Cuentas</router-link>
          <router-link to="/app/asientos" class="nav-item" active-class="nav-active">✏️ Asientos</router-link>
          <router-link to="/app/reportes" class="nav-item" active-class="nav-active">📈 Reportes</router-link>
        </div>

        <div class="mt-3">
          <p class="text-blue-400 text-xs uppercase px-3 mb-1 font-semibold">Ventas</p>
          <router-link to="/app/clientes" class="nav-item" active-class="nav-active">👥 Clientes</router-link>
          <router-link to="/app/productos" class="nav-item" active-class="nav-active">📦 Productos</router-link>
          <router-link to="/app/ventas" class="nav-item" active-class="nav-active">🧾 Facturas Venta</router-link>
          <router-link to="/app/cobros" class="nav-item" active-class="nav-active">💰 Cobros</router-link>
        </div>

        <div class="mt-3">
          <p class="text-blue-400 text-xs uppercase px-3 mb-1 font-semibold">Compras</p>
          <router-link to="/app/proveedores" class="nav-item" active-class="nav-active">🏭 Proveedores</router-link>
          <router-link to="/app/compras" class="nav-item" active-class="nav-active">🛒 Facturas Compra</router-link>
        </div>

        <div class="mt-3">
          <p class="text-blue-400 text-xs uppercase px-3 mb-1 font-semibold">SRI</p>
          <router-link to="/app/sri" class="nav-item" active-class="nav-active">🏛️ Módulo SRI</router-link>
        </div>
      </nav>

      <div class="p-3 border-t border-blue-800">
        <div class="text-sm text-blue-200 mb-2">{{ auth.user?.nombres }}</div>
        <div class="text-xs text-blue-400 mb-2 capitalize">{{ auth.user?.rol }}</div>
        <button @click="handleLogout" class="w-full text-left text-sm text-red-300 hover:text-red-100">
          🚪 Cerrar Sesión
        </button>
      </div>
    </aside>

    <!-- Main content -->
    <main class="flex-1 overflow-auto">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

function cambiarEmpresa(event) {
  const empresaId = parseInt(event.target.value)
  const empresa = auth.user.empresas.find(e => e.id === empresaId)
  if (empresa) auth.setEmpresaActual(empresa)
}

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>

<style scoped>
.nav-item {
  @apply flex items-center gap-2 px-3 py-2 rounded text-sm text-blue-200 hover:bg-blue-800 hover:text-white transition w-full block;
}
.nav-active {
  @apply bg-blue-700 text-white;
}
</style>
