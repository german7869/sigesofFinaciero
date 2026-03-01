<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
        <p class="text-gray-500 text-sm">Empresa Activa</p>
        <p class="text-xl font-bold text-gray-800 mt-1">{{ auth.empresaActual?.razon_social || 'Sin empresa' }}</p>
      </div>
      <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
        <p class="text-gray-500 text-sm">Usuario</p>
        <p class="text-xl font-bold text-gray-800 mt-1">{{ auth.user?.nombres }}</p>
      </div>
      <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
        <p class="text-gray-500 text-sm">Rol</p>
        <p class="text-xl font-bold text-gray-800 mt-1 capitalize">{{ auth.user?.rol }}</p>
      </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Accesos Rápidos</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <router-link to="/app/ventas/nueva" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
          <span class="text-3xl mb-2">🧾</span>
          <span class="text-sm font-medium text-blue-700">Nueva Venta</span>
        </router-link>
        <router-link to="/app/cobros/nuevo" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
          <span class="text-3xl mb-2">💰</span>
          <span class="text-sm font-medium text-green-700">Nuevo Cobro</span>
        </router-link>
        <router-link to="/app/compras/nueva" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
          <span class="text-3xl mb-2">🛒</span>
          <span class="text-sm font-medium text-orange-700">Nueva Compra</span>
        </router-link>
        <router-link to="/app/asientos/nuevo" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
          <span class="text-3xl mb-2">✏️</span>
          <span class="text-sm font-medium text-purple-700">Nuevo Asiento</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
</script>
