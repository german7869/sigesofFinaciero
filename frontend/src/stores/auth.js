import { defineStore } from 'pinia'
import api from '@/api/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    token: localStorage.getItem('token') || null,
    empresaActual: JSON.parse(localStorage.getItem('empresaActual') || 'null'),
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.rol === 'admin',
    isContador: (state) => state.user?.rol === 'contador' || state.user?.rol === 'admin',
  },
  actions: {
    async login(correo, password) {
      const { data } = await api.post('/login', { correo, password })
      this.token = data.token
      this.user = data.user
      localStorage.setItem('token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))
      if (data.user.empresas?.length > 0) {
        this.setEmpresaActual(data.user.empresas[0])
      }
    },
    async logout() {
      try { await api.post('/logout') } catch {}
      this.token = null
      this.user = null
      this.empresaActual = null
      localStorage.clear()
    },
    setEmpresaActual(empresa) {
      this.empresaActual = empresa
      localStorage.setItem('empresaActual', JSON.stringify(empresa))
    },
    async fetchMe() {
      const { data } = await api.get('/me')
      this.user = data
      localStorage.setItem('user', JSON.stringify(data))
      if (data.empresas?.length > 0 && !this.empresaActual) {
        this.setEmpresaActual(data.empresas[0])
      }
    }
  }
})
