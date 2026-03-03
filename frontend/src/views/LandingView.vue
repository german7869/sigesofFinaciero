<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

function goToLogin() {
  router.push({ name: 'login' })
}

function goToDashboard() {
  router.push({ name: 'dashboard' })
}
</script>

<template>
  <div class="landing">
    <header class="landing-header">
      <div class="logo">
        <span class="logo-icon">💼</span>
        <span class="logo-text">SIGE Soft Financiero</span>
      </div>
      <nav>
        <button v-if="auth.isAuthenticated" class="btn-primary" @click="goToDashboard">
          Ir al Dashboard
        </button>
        <button v-else class="btn-primary" @click="goToLogin">
          Iniciar Sesión
        </button>
      </nav>
    </header>

    <main class="landing-main">
      <section class="hero">
        <h1>Sistema de Gestión Financiera</h1>
        <p class="subtitle">
          Administra tus finanzas empresariales de forma sencilla, segura y multiempresa.
        </p>
        <button class="btn-cta" @click="goToLogin">
          Comenzar ahora →
        </button>
      </section>

      <section class="features">
        <div class="feature-card">
          <span class="feature-icon">🔐</span>
          <h3>Acceso Seguro</h3>
          <p>Autenticación por roles: administrador, contador, auxiliar y cajero.</p>
        </div>
        <div class="feature-card">
          <span class="feature-icon">🏢</span>
          <h3>Multiempresa</h3>
          <p>Gestiona múltiples empresas desde una sola cuenta.</p>
        </div>
        <div class="feature-card">
          <span class="feature-icon">📊</span>
          <h3>Control Financiero</h3>
          <p>Módulos contables, reportes y más próximamente.</p>
        </div>
      </section>
    </main>

    <footer class="landing-footer">
      <p>© 2026 SIGE Soft Financiero. Todos los derechos reservados.</p>
    </footer>
  </div>
</template>

<style scoped>
.landing {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: linear-gradient(135deg, #1a365d 0%, #2d6a4f 100%);
  color: #fff;
}

.landing-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.2rem 2rem;
  background: rgba(0, 0, 0, 0.15);
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.3rem;
  font-weight: 700;
}

.logo-icon {
  font-size: 1.6rem;
}

.btn-primary {
  background: rgba(255, 255, 255, 0.15);
  border: 2px solid rgba(255, 255, 255, 0.5);
  color: #fff;
  padding: 0.5rem 1.4rem;
  border-radius: 6px;
  font-size: 0.95rem;
  font-weight: 600;
  transition: background 0.2s;
}

.btn-primary:hover {
  background: rgba(255, 255, 255, 0.3);
}

.landing-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 2rem;
  gap: 3rem;
}

.hero {
  text-align: center;
  max-width: 650px;
}

.hero h1 {
  font-size: 2.8rem;
  font-weight: 800;
  margin-bottom: 1rem;
}

.subtitle {
  font-size: 1.15rem;
  opacity: 0.85;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.btn-cta {
  background: #f6ad55;
  border: none;
  color: #1a202c;
  padding: 0.85rem 2.5rem;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 700;
  transition: background 0.2s, transform 0.1s;
}

.btn-cta:hover {
  background: #ed8936;
  transform: translateY(-2px);
}

.features {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
  justify-content: center;
}

.feature-card {
  background: rgba(255, 255, 255, 0.12);
  border-radius: 12px;
  padding: 1.8rem 2rem;
  width: 220px;
  text-align: center;
  backdrop-filter: blur(4px);
}

.feature-icon {
  font-size: 2.2rem;
  display: block;
  margin-bottom: 0.8rem;
}

.feature-card h3 {
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.feature-card p {
  font-size: 0.9rem;
  opacity: 0.8;
  line-height: 1.5;
}

.landing-footer {
  text-align: center;
  padding: 1.2rem;
  font-size: 0.85rem;
  opacity: 0.6;
}
</style>
