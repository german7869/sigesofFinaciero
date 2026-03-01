<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const menuItems = computed(() => {
  const rol = auth.rol
  const all = [
    {
      label: 'Dashboard',
      icon: '🏠',
      route: `dashboard-${rol}`,
      roles: ['admin', 'contador', 'auxiliar', 'cajero'],
    },
    {
      label: 'Usuarios',
      icon: '👥',
      route: 'dashboard-admin',
      roles: ['admin'],
    },
    {
      label: 'Empresas',
      icon: '🏢',
      route: 'dashboard-admin',
      roles: ['admin'],
    },
    {
      label: 'Contabilidad',
      icon: '📒',
      route: 'dashboard-contador',
      roles: ['admin', 'contador'],
    },
    {
      label: 'Auxiliar',
      icon: '📋',
      route: 'dashboard-auxiliar',
      roles: ['admin', 'auxiliar'],
    },
    {
      label: 'Caja',
      icon: '💰',
      route: 'dashboard-cajero',
      roles: ['admin', 'cajero'],
    },
  ]
  return all.filter((item) => item.roles.includes(rol))
})

async function handleLogout() {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="app-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-brand">
        <span class="brand-icon">💼</span>
        <span class="brand-name">SIGE Soft</span>
      </div>

      <div class="empresa-info" v-if="auth.empresa">
        <span class="empresa-icon">🏢</span>
        <span class="empresa-name">{{ auth.empresa.nombre }}</span>
      </div>

      <nav class="sidebar-nav">
        <router-link
          v-for="item in menuItems"
          :key="item.label"
          :to="{ name: item.route }"
          class="nav-item"
          active-class="nav-item--active"
        >
          <span class="nav-icon">{{ item.icon }}</span>
          <span class="nav-label">{{ item.label }}</span>
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <div class="user-info">
          <div class="user-avatar">{{ auth.user?.name?.charAt(0)?.toUpperCase() }}</div>
          <div class="user-details">
            <span class="user-name">{{ auth.user?.name }}</span>
            <span class="user-role">{{ auth.rol }}</span>
          </div>
        </div>
        <button class="btn-logout" @click="handleLogout" title="Cerrar sesión">
          🚪
        </button>
      </div>
    </aside>

    <!-- Main content -->
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
}

/* Sidebar */
.sidebar {
  width: 240px;
  min-width: 240px;
  background: #1a365d;
  color: #fff;
  display: flex;
  flex-direction: column;
  padding: 1.2rem 0;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.5rem 1.4rem 1.2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}

.brand-icon {
  font-size: 1.5rem;
}

.brand-name {
  font-size: 1.1rem;
  font-weight: 700;
}

.empresa-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.4rem;
  background: rgba(255, 255, 255, 0.07);
  margin: 0.8rem 0.8rem 0.4rem;
  border-radius: 8px;
  font-size: 0.82rem;
  opacity: 0.85;
}

.empresa-name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sidebar-nav {
  flex: 1;
  padding: 0.5rem 0.8rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  padding: 0.65rem 0.9rem;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.75);
  transition: background 0.15s, color 0.15s;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.nav-item--active {
  background: rgba(255, 255, 255, 0.18);
  color: #fff;
  font-weight: 700;
}

.nav-icon {
  font-size: 1.1rem;
  width: 1.4rem;
  text-align: center;
}

.sidebar-footer {
  padding: 1rem 1rem 0.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex: 1;
  min-width: 0;
}

.user-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #2d6a4f;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.95rem;
  flex-shrink: 0;
}

.user-details {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.user-name {
  font-size: 0.82rem;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: 0.72rem;
  opacity: 0.65;
  text-transform: capitalize;
}

.btn-logout {
  background: transparent;
  border: none;
  font-size: 1.3rem;
  padding: 0.3rem;
  border-radius: 6px;
  transition: background 0.15s;
}

.btn-logout:hover {
  background: rgba(255, 255, 255, 0.1);
}

/* Main */
.main-content {
  flex: 1;
  padding: 2rem;
  background: #f0f4f8;
  overflow-y: auto;
}
</style>
