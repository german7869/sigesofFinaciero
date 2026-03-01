<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

const roleRoutes = {
  admin: 'dashboard-admin',
  contador: 'dashboard-contador',
  auxiliar: 'dashboard-auxiliar',
  cajero: 'dashboard-cajero',
}

// Auto-redirect
if (auth.rol && roleRoutes[auth.rol]) {
  router.replace({ name: roleRoutes[auth.rol] })
}
</script>

<template>
  <div class="dashboard">
    <h1>Redirigiendo...</h1>
  </div>
</template>
