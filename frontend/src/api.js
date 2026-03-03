import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Attach token from localStorage if available
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  const empresaId = localStorage.getItem('empresa_id')
  if (empresaId) {
    config.headers['X-Empresa-Id'] = empresaId
  }
  return config
})

export default api
