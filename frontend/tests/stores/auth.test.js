import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/stores/auth'

// Mock the api module so we never make real HTTP calls
vi.mock('@/api', () => ({
  default: {
    post: vi.fn(),
    get: vi.fn(),
  },
}))

import api from '@/api'

describe('useAuthStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  // -----------------------------------------------------------------------
  // Initial state
  // -----------------------------------------------------------------------

  it('starts unauthenticated when localStorage is empty', () => {
    const store = useAuthStore()
    expect(store.isAuthenticated).toBe(false)
    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(store.empresa).toBeNull()
  })

  it('reads token and user from localStorage on init', () => {
    const user = { id: 1, name: 'Admin', email: 'a@b.com', rol: 'admin', empresa: null }
    localStorage.setItem('token', 'test-token')
    localStorage.setItem('user', JSON.stringify(user))

    setActivePinia(createPinia())
    const store = useAuthStore()

    expect(store.isAuthenticated).toBe(true)
    expect(store.token).toBe('test-token')
    expect(store.user.email).toBe('a@b.com')
  })

  // -----------------------------------------------------------------------
  // login()
  // -----------------------------------------------------------------------

  it('login() stores token, user and empresa in state and localStorage', async () => {
    const fakeUser = {
      id: 1,
      name: 'Administrador',
      email: 'admin@gmail.com',
      rol: 'admin',
      empresa: { id: 1, nombre: 'Demo S.A.' },
    }
    api.post.mockResolvedValueOnce({
      data: { token: 'sanctum-abc', user: fakeUser },
    })

    const store = useAuthStore()
    const result = await store.login('admin@gmail.com', 'admin1234')

    expect(api.post).toHaveBeenCalledWith('/login', {
      email: 'admin@gmail.com',
      password: 'admin1234',
    })
    expect(store.token).toBe('sanctum-abc')
    expect(store.user.rol).toBe('admin')
    expect(store.empresa.nombre).toBe('Demo S.A.')
    expect(store.isAuthenticated).toBe(true)
    expect(localStorage.getItem('token')).toBe('sanctum-abc')
    expect(JSON.parse(localStorage.getItem('empresa')).nombre).toBe('Demo S.A.')
    expect(result.token).toBe('sanctum-abc')
  })

  it('login() exposes rol computed property', async () => {
    api.post.mockResolvedValueOnce({
      data: {
        token: 'tok',
        user: { id: 2, name: 'Cont', email: 'c@c.com', rol: 'contador', empresa: null },
      },
    })

    const store = useAuthStore()
    await store.login('c@c.com', 'pass')

    expect(store.rol).toBe('contador')
  })

  it('login() propagates API errors to the caller', async () => {
    api.post.mockRejectedValueOnce(new Error('Network error'))

    const store = useAuthStore()
    await expect(store.login('x@x.com', 'pass')).rejects.toThrow('Network error')

    // State must remain clean
    expect(store.isAuthenticated).toBe(false)
  })

  // -----------------------------------------------------------------------
  // fetchMe()
  // -----------------------------------------------------------------------

  it('fetchMe() updates user and empresa from API', async () => {
    const meData = {
      id: 1,
      name: 'Admin',
      email: 'a@a.com',
      rol: 'admin',
      empresa: { id: 3, nombre: 'Empresa 3' },
    }
    api.get.mockResolvedValueOnce({ data: meData })
    localStorage.setItem('token', 'existing-token')
    setActivePinia(createPinia())

    const store = useAuthStore()
    const result = await store.fetchMe()

    expect(api.get).toHaveBeenCalledWith('/me')
    expect(store.user.name).toBe('Admin')
    expect(store.empresa.nombre).toBe('Empresa 3')
    expect(result.rol).toBe('admin')
  })

  // -----------------------------------------------------------------------
  // logout()
  // -----------------------------------------------------------------------

  it('logout() clears state and localStorage', async () => {
    // Seed localStorage as if the user is logged in
    localStorage.setItem('token', 'tok')
    localStorage.setItem('user', JSON.stringify({ id: 1 }))
    localStorage.setItem('empresa', JSON.stringify({ id: 1 }))
    localStorage.setItem('empresa_id', '1')

    setActivePinia(createPinia())
    const store = useAuthStore()

    api.post.mockResolvedValueOnce({ data: { message: 'ok' } })
    await store.logout()

    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(store.empresa).toBeNull()
    expect(store.isAuthenticated).toBe(false)
    expect(localStorage.getItem('token')).toBeNull()
    expect(localStorage.getItem('empresa_id')).toBeNull()
  })

  it('logout() clears state even when API call fails', async () => {
    localStorage.setItem('token', 'tok')
    setActivePinia(createPinia())

    const store = useAuthStore()
    api.post.mockRejectedValueOnce(new Error('Network error'))

    await store.logout()

    expect(store.isAuthenticated).toBe(false)
    expect(localStorage.getItem('token')).toBeNull()
  })
})
