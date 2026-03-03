import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    name: 'landing',
    component: () => import('@/views/LandingView.vue'),
    meta: { public: true },
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: { public: true },
  },
  {
    path: '/dashboard',
    component: () => import('@/layouts/AppLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'dashboard',
        component: () => import('@/views/dashboards/DashboardView.vue'),
      },
      {
        path: 'admin',
        name: 'dashboard-admin',
        component: () => import('@/views/dashboards/AdminDashboard.vue'),
        meta: { roles: ['admin'] },
      },
      {
        path: 'contador',
        name: 'dashboard-contador',
        component: () => import('@/views/dashboards/ContadorDashboard.vue'),
        meta: { roles: ['contador'] },
      },
      {
        path: 'auxiliar',
        name: 'dashboard-auxiliar',
        component: () => import('@/views/dashboards/AuxiliarDashboard.vue'),
        meta: { roles: ['auxiliar'] },
      },
      {
        path: 'cajero',
        name: 'dashboard-cajero',
        component: () => import('@/views/dashboards/CajeroDashboard.vue'),
        meta: { roles: ['cajero'] },
      },
    ],
  },
  { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, _from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next({ name: 'login' })
  }

  if (to.meta.public && auth.isAuthenticated && to.name === 'login') {
    return redirectByRole(auth.rol, next)
  }

  if (to.name === 'dashboard' && auth.isAuthenticated) {
    return redirectByRole(auth.rol, next)
  }

  if (to.meta.roles && !to.meta.roles.includes(auth.rol)) {
    return redirectByRole(auth.rol, next)
  }

  next()
})

function redirectByRole(rol, next) {
  const roleRoutes = {
    admin: 'dashboard-admin',
    contador: 'dashboard-contador',
    auxiliar: 'dashboard-auxiliar',
    cajero: 'dashboard-cajero',
  }
  next({ name: roleRoutes[rol] || 'landing' })
}

export default router
