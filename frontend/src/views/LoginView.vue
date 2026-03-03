<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

async function handleLogin() {
  error.value = ''
  loading.value = true
  try {
    await auth.login(form.email, form.password)
    // Redirect based on role
    const roleRoutes = {
      admin: 'dashboard-admin',
      contador: 'dashboard-contador',
      auxiliar: 'dashboard-auxiliar',
      cajero: 'dashboard-cajero',
    }
    router.push({ name: roleRoutes[auth.rol] || 'dashboard' })
  } catch (err) {
    if (err.response?.data?.errors?.email) {
      error.value = err.response.data.errors.email[0]
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error al iniciar sesión. Intente nuevamente.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-page">
    <div class="login-card">
      <div class="login-brand">
        <span class="brand-icon">💼</span>
        <h1>SIGE Soft Financiero</h1>
      </div>
      <h2>Iniciar Sesión</h2>

      <form class="login-form" @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="email">Correo electrónico</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="usuario@empresa.com"
            required
            autocomplete="email"
          />
        </div>

        <div class="form-group">
          <label for="password">Contraseña</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="••••••••"
            required
            autocomplete="current-password"
          />
        </div>

        <div v-if="error" class="error-alert">
          {{ error }}
        </div>

        <button type="submit" class="btn-login" :disabled="loading">
          {{ loading ? 'Ingresando...' : 'Ingresar' }}
        </button>
      </form>

      <div class="back-link">
        <router-link to="/">← Volver al inicio</router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #1a365d 0%, #2d6a4f 100%);
  padding: 1rem;
}

.login-card {
  background: #fff;
  border-radius: 14px;
  padding: 2.5rem 2.5rem;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.login-brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 1.5rem;
}

.brand-icon {
  font-size: 2rem;
}

.login-brand h1 {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a365d;
  line-height: 1.2;
}

h2 {
  font-size: 1.6rem;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 1.8rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #4a5568;
}

input {
  border: 1.5px solid #cbd5e0;
  border-radius: 7px;
  padding: 0.7rem 0.9rem;
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.2s;
  color: #1a202c;
}

input:focus {
  border-color: #2d6a4f;
  box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.15);
}

.error-alert {
  background: #fff5f5;
  border: 1px solid #feb2b2;
  color: #c53030;
  padding: 0.75rem 1rem;
  border-radius: 7px;
  font-size: 0.875rem;
}

.btn-login {
  background: #2d6a4f;
  color: #fff;
  border: none;
  border-radius: 7px;
  padding: 0.85rem;
  font-size: 1rem;
  font-weight: 700;
  transition: background 0.2s, transform 0.1s;
  margin-top: 0.3rem;
}

.btn-login:hover:not(:disabled) {
  background: #1b4332;
  transform: translateY(-1px);
}

.btn-login:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.back-link {
  text-align: center;
  margin-top: 1.5rem;
  font-size: 0.875rem;
}

.back-link a {
  color: #2d6a4f;
  font-weight: 600;
}

.back-link a:hover {
  text-decoration: underline;
}
</style>
