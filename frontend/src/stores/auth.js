import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const token = ref(localStorage.getItem('token') || null)
  const empresa = ref(JSON.parse(localStorage.getItem('empresa') || 'null'))

  const isAuthenticated = computed(() => !!token.value)
  const rol = computed(() => user.value?.rol || null)

  async function login(email, password) {
    const response = await api.post('/login', { email, password })
    token.value = response.data.token
    user.value = response.data.user
    empresa.value = response.data.user.empresa

    localStorage.setItem('token', token.value)
    localStorage.setItem('user', JSON.stringify(user.value))
    localStorage.setItem('empresa', JSON.stringify(empresa.value))
    if (empresa.value?.id) {
      localStorage.setItem('empresa_id', empresa.value.id)
    }

    return response.data
  }

  async function fetchMe() {
    const response = await api.get('/me')
    user.value = response.data
    empresa.value = response.data.empresa
    localStorage.setItem('user', JSON.stringify(user.value))
    localStorage.setItem('empresa', JSON.stringify(empresa.value))
    return response.data
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch {
      // ignore errors on logout
    } finally {
      token.value = null
      user.value = null
      empresa.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      localStorage.removeItem('empresa')
      localStorage.removeItem('empresa_id')
    }
  }

  return { user, token, empresa, isAuthenticated, rol, login, fetchMe, logout }
})
