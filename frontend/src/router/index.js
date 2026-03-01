import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  { path: '/', name: 'landing', component: () => import('@/views/LandingView.vue'), meta: { public: true } },
  { path: '/login', name: 'login', component: () => import('@/views/LoginView.vue'), meta: { public: true } },
  {
    path: '/app',
    component: () => import('@/layouts/AppLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: '', redirect: '/app/dashboard' },
      { path: 'dashboard', name: 'dashboard', component: () => import('@/views/DashboardView.vue') },
      { path: 'empresas', name: 'empresas', component: () => import('@/views/admin/EmpresasView.vue') },
      { path: 'usuarios', name: 'usuarios', component: () => import('@/views/admin/UsuariosView.vue') },
      { path: 'documentos', name: 'documentos', component: () => import('@/views/admin/DocumentosView.vue') },
      { path: 'formas-pago', name: 'formas-pago', component: () => import('@/views/admin/FormasPagoView.vue') },
      { path: 'impuestos-iva', name: 'impuestos-iva', component: () => import('@/views/admin/ImpuestosIvaView.vue') },
      { path: 'modelos-contables', name: 'modelos-contables', component: () => import('@/views/admin/ModelosContablesView.vue') },
      { path: 'permisos', name: 'permisos', component: () => import('@/views/admin/PermisosView.vue') },
      { path: 'cuentas-contables', name: 'cuentas-contables', component: () => import('@/views/contabilidad/CuentasContablesView.vue') },
      { path: 'asientos', name: 'asientos', component: () => import('@/views/contabilidad/AsientosView.vue') },
      { path: 'asientos/nuevo', name: 'asiento-nuevo', component: () => import('@/views/contabilidad/AsientoFormView.vue') },
      { path: 'reportes', name: 'reportes', component: () => import('@/views/contabilidad/ReportesView.vue') },
      { path: 'clientes', name: 'clientes', component: () => import('@/views/ventas/ClientesView.vue') },
      { path: 'productos', name: 'productos', component: () => import('@/views/ventas/ProductosView.vue') },
      { path: 'ventas', name: 'ventas', component: () => import('@/views/ventas/VentasView.vue') },
      { path: 'ventas/nueva', name: 'venta-nueva', component: () => import('@/views/ventas/VentaFormView.vue') },
      { path: 'ventas/:id', name: 'venta-detalle', component: () => import('@/views/ventas/VentaDetalleView.vue') },
      { path: 'cobros', name: 'cobros', component: () => import('@/views/cobros/CobrosView.vue') },
      { path: 'cobros/nuevo', name: 'cobro-nuevo', component: () => import('@/views/cobros/CobroFormView.vue') },
      { path: 'proveedores', name: 'proveedores', component: () => import('@/views/compras/ProveedoresView.vue') },
      { path: 'compras', name: 'compras', component: () => import('@/views/compras/ComprasView.vue') },
      { path: 'compras/nueva', name: 'compra-nueva', component: () => import('@/views/compras/CompraFormView.vue') },
      { path: 'compras/:id', name: 'compra-detalle', component: () => import('@/views/compras/CompraDetalleView.vue') },
      { path: 'sri', name: 'sri', component: () => import('@/views/SriView.vue') },
    ]
  },
  { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/login')
  } else if (to.path === '/login' && auth.isAuthenticated) {
    next('/app/dashboard')
  } else {
    next()
  }
})

export default router
