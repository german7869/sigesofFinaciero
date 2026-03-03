import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createRouter, createMemoryHistory } from 'vue-router'
import { setActivePinia, createPinia } from 'pinia'
import { defineComponent } from 'vue'

// -----------------------------------------------------------------------
// Helper: build a minimal router that replicates the production guards
// without needing the full component tree.
// -----------------------------------------------------------------------

const Stub = defineComponent({ template: '<div />' })

function buildRouter(authState = {}) {
  const { isAuthenticated = false, rol = null } = authState

  // Replicate the guard logic from src/router/index.js
  function redirectByRole(r, next) {
    const map = {
      admin: 'dashboard-admin',
      contador: 'dashboard-contador',
      auxiliar: 'dashboard-auxiliar',
      cajero: 'dashboard-cajero',
    }
    next({ name: map[r] || 'landing' })
  }

  const routes = [
    { path: '/', name: 'landing', component: Stub, meta: { public: true } },
    { path: '/login', name: 'login', component: Stub, meta: { public: true } },
    {
      path: '/dashboard',
      component: Stub,
      meta: { requiresAuth: true },
      children: [
        { path: '', name: 'dashboard', component: Stub },
        { path: 'admin', name: 'dashboard-admin', component: Stub, meta: { roles: ['admin'] } },
        {
          path: 'contador',
          name: 'dashboard-contador',
          component: Stub,
          meta: { roles: ['contador'] },
        },
        {
          path: 'auxiliar',
          name: 'dashboard-auxiliar',
          component: Stub,
          meta: { roles: ['auxiliar'] },
        },
        { path: 'cajero', name: 'dashboard-cajero', component: Stub, meta: { roles: ['cajero'] } },
      ],
    },
    { path: '/:pathMatch(.*)*', redirect: '/' },
  ]

  const router = createRouter({ history: createMemoryHistory(), routes })

  router.beforeEach((to, _from, next) => {
    if (to.meta.requiresAuth && !isAuthenticated) return next({ name: 'login' })
    if (to.meta.public && isAuthenticated && to.name === 'login')
      return redirectByRole(rol, next)
    if (to.name === 'dashboard' && isAuthenticated) return redirectByRole(rol, next)
    if (to.meta.roles && !to.meta.roles.includes(rol)) return redirectByRole(rol, next)
    next()
  })

  return router
}

describe('Router guards', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  // -----------------------------------------------------------------------
  // Unauthenticated
  // -----------------------------------------------------------------------

  it('redirects to login when accessing protected route unauthenticated', async () => {
    const router = buildRouter({ isAuthenticated: false })
    await router.push('/dashboard/admin')
    expect(router.currentRoute.value.name).toBe('login')
  })

  it('allows landing when unauthenticated', async () => {
    const router = buildRouter({ isAuthenticated: false })
    await router.push('/')
    expect(router.currentRoute.value.name).toBe('landing')
  })

  it('allows login page when unauthenticated', async () => {
    const router = buildRouter({ isAuthenticated: false })
    await router.push('/login')
    expect(router.currentRoute.value.name).toBe('login')
  })

  // -----------------------------------------------------------------------
  // Authenticated — redirect away from login
  // -----------------------------------------------------------------------

  it('admin visiting /login is redirected to dashboard-admin', async () => {
    const router = buildRouter({ isAuthenticated: true, rol: 'admin' })
    await router.push('/login')
    expect(router.currentRoute.value.name).toBe('dashboard-admin')
  })

  it('contador visiting /login is redirected to dashboard-contador', async () => {
    const router = buildRouter({ isAuthenticated: true, rol: 'contador' })
    await router.push('/login')
    expect(router.currentRoute.value.name).toBe('dashboard-contador')
  })

  // -----------------------------------------------------------------------
  // Role-based access
  // -----------------------------------------------------------------------

  it('admin can access dashboard-admin', async () => {
    const router = buildRouter({ isAuthenticated: true, rol: 'admin' })
    await router.push('/dashboard/admin')
    expect(router.currentRoute.value.name).toBe('dashboard-admin')
  })

  it('contador is redirected away from dashboard-admin', async () => {
    const router = buildRouter({ isAuthenticated: true, rol: 'contador' })
    await router.push('/dashboard/admin')
    expect(router.currentRoute.value.name).toBe('dashboard-contador')
  })

  it('auxiliar is redirected away from dashboard-admin', async () => {
    const router = buildRouter({ isAuthenticated: true, rol: 'auxiliar' })
    await router.push('/dashboard/admin')
    expect(router.currentRoute.value.name).toBe('dashboard-auxiliar')
  })

  it('cajero is redirected away from dashboard-contador', async () => {
    const router = buildRouter({ isAuthenticated: true, rol: 'cajero' })
    await router.push('/dashboard/contador')
    expect(router.currentRoute.value.name).toBe('dashboard-cajero')
  })

  // -----------------------------------------------------------------------
  // /dashboard generic redirect
  // -----------------------------------------------------------------------

  it('authenticated admin visiting /dashboard is redirected to dashboard-admin', async () => {
    const router = buildRouter({ isAuthenticated: true, rol: 'admin' })
    await router.push('/dashboard')
    expect(router.currentRoute.value.name).toBe('dashboard-admin')
  })

  it('authenticated contador visiting /dashboard is redirected to dashboard-contador', async () => {
    const router = buildRouter({ isAuthenticated: true, rol: 'contador' })
    await router.push('/dashboard')
    expect(router.currentRoute.value.name).toBe('dashboard-contador')
  })

  // -----------------------------------------------------------------------
  // 404 catch-all
  // -----------------------------------------------------------------------

  it('unknown route redirects to landing', async () => {
    const router = buildRouter({ isAuthenticated: false })
    await router.push('/ruta/inexistente')
    expect(router.currentRoute.value.path).toBe('/')
  })
})
