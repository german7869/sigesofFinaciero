<script setup>
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
</script>

<template>
  <div class="dashboard-page">
    <div class="page-header">
      <h1>🛡️ Panel de Administrador</h1>
      <p class="welcome">Bienvenido, <strong>{{ auth.user?.name }}</strong></p>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <span class="stat-icon">👥</span>
        <div>
          <div class="stat-value">—</div>
          <div class="stat-label">Usuarios</div>
        </div>
      </div>
      <div class="stat-card">
        <span class="stat-icon">🏢</span>
        <div>
          <div class="stat-value">—</div>
          <div class="stat-label">Empresas</div>
        </div>
      </div>
      <div class="stat-card">
        <span class="stat-icon">📊</span>
        <div>
          <div class="stat-value">—</div>
          <div class="stat-label">Transacciones</div>
        </div>
      </div>
      <div class="stat-card">
        <span class="stat-icon">✅</span>
        <div>
          <div class="stat-value">Activo</div>
          <div class="stat-label">Estado del sistema</div>
        </div>
      </div>
    </div>

    <div class="info-banner">
      <p>
        ⚙️ <strong>Módulos en desarrollo:</strong> Gestión de usuarios, empresas, reportes y
        módulos contables estarán disponibles próximamente.
      </p>
    </div>
  </div>
</template>

<style scoped>
.dashboard-page {
  max-width: 900px;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.8rem;
  font-weight: 800;
  color: #1a365d;
}

.welcome {
  color: #4a5568;
  margin-top: 0.3rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 1.2rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: #fff;
  border-radius: 12px;
  padding: 1.4rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
}

.stat-icon {
  font-size: 2rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a365d;
}

.stat-label {
  font-size: 0.8rem;
  color: #718096;
  margin-top: 0.1rem;
}

.info-banner {
  background: #ebf8ff;
  border: 1px solid #bee3f8;
  border-radius: 10px;
  padding: 1rem 1.4rem;
  color: #2b6cb0;
  font-size: 0.9rem;
  line-height: 1.5;
}
</style>
