import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { setActivePinia, createPinia } from 'pinia'
import LoginView from '@/views/LoginView.vue'

// Stub vue-router
const mockPush = vi.fn()
vi.mock('vue-router', () => ({
  useRouter: () => ({ push: mockPush }),
}))

// Stub the auth store's login action
const mockLogin = vi.fn()
vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({
    login: mockLogin,
    rol: 'admin',
  }),
}))

describe('LoginView', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  function mountLogin() {
    return mount(LoginView, {
      global: {
        stubs: { RouterLink: true },
      },
    })
  }

  // -----------------------------------------------------------------------
  // Rendering
  // -----------------------------------------------------------------------

  it('renders the login form', () => {
    const wrapper = mountLogin()
    expect(wrapper.find('h2').text()).toBe('Iniciar Sesión')
    expect(wrapper.find('input#email').exists()).toBe(true)
    expect(wrapper.find('input#password').exists()).toBe(true)
    expect(wrapper.find('button[type="submit"]').text()).toBe('Ingresar')
  })

  it('shows the app name', () => {
    const wrapper = mountLogin()
    expect(wrapper.text()).toContain('SIGE Soft Financiero')
  })

  // -----------------------------------------------------------------------
  // Form interaction
  // -----------------------------------------------------------------------

  it('two-way-binds email and password inputs', async () => {
    const wrapper = mountLogin()

    await wrapper.find('input#email').setValue('admin@example.com')
    await wrapper.find('input#password').setValue('secret123')

    const emailInput = wrapper.find('input#email')
    const passInput = wrapper.find('input#password')

    expect(emailInput.element.value).toBe('admin@example.com')
    expect(passInput.element.value).toBe('secret123')
  })

  it('calls auth.login and redirects on success', async () => {
    mockLogin.mockResolvedValueOnce({})
    const wrapper = mountLogin()

    await wrapper.find('input#email').setValue('admin@gmail.com')
    await wrapper.find('input#password').setValue('admin1234')
    await wrapper.find('form').trigger('submit')

    // Wait for async login to settle
    await wrapper.vm.$nextTick()

    expect(mockLogin).toHaveBeenCalledWith('admin@gmail.com', 'admin1234')
    expect(mockPush).toHaveBeenCalledWith({ name: 'dashboard-admin' })
  })

  it('shows validation error from API response', async () => {
    const apiError = {
      response: {
        data: {
          errors: { email: ['Las credenciales no son correctas.'] },
        },
      },
    }
    mockLogin.mockRejectedValueOnce(apiError)

    const wrapper = mountLogin()
    await wrapper.find('input#email').setValue('bad@x.com')
    await wrapper.find('input#password').setValue('wrong')
    await wrapper.find('form').trigger('submit')
    await wrapper.vm.$nextTick()

    expect(wrapper.text()).toContain('Las credenciales no son correctas.')
  })

  it('shows generic error when no specific API error message', async () => {
    mockLogin.mockRejectedValueOnce(new Error('Network error'))

    const wrapper = mountLogin()
    await wrapper.find('form').trigger('submit')
    await wrapper.vm.$nextTick()

    expect(wrapper.text()).toContain('Error al iniciar sesión. Intente nuevamente.')
  })

  it('shows error from response.data.message when no errors object', async () => {
    const apiError = {
      response: {
        data: { message: 'Servidor no disponible.' },
      },
    }
    mockLogin.mockRejectedValueOnce(apiError)

    const wrapper = mountLogin()
    await wrapper.find('form').trigger('submit')
    await wrapper.vm.$nextTick()

    expect(wrapper.text()).toContain('Servidor no disponible.')
  })

  it('submit button shows loading text while submitting', async () => {
    let resolveLogin
    mockLogin.mockReturnValueOnce(
      new Promise((resolve) => {
        resolveLogin = resolve
      })
    )

    const wrapper = mountLogin()
    await wrapper.find('form').trigger('submit')
    await wrapper.vm.$nextTick()

    expect(wrapper.find('button[type="submit"]').text()).toBe('Ingresando...')

    // Resolve the promise and check button is back to normal
    resolveLogin({})
    await wrapper.vm.$nextTick()
    await wrapper.vm.$nextTick()

    expect(wrapper.find('button[type="submit"]').text()).toBe('Ingresar')
  })
})
