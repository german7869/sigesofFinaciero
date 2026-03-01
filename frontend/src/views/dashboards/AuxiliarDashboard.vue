<script setup>
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
</script>

<template>
  <div class="dashboard-page">
    <div class="page-header">
      <h1>📋 Panel de Auxiliar</h1>
      <p class="welcome">Bienvenido, <strong>{{ auth.user?.name }}</strong></p>
    </div>

    <div class="info-banner">
      <p>
        📋 <strong>Módulos auxiliares en desarrollo:</strong> Registro de documentos, soporte
        contable y tareas asignadas estarán disponibles próximamente.
      </p>
    </div>
  </div>
</template>

<style scoped>
.dashboard-page { max-width: 900px; }
.page-header { margin-bottom: 2rem; }
.page-header h1 { font-size: 1.8rem; font-weight: 800; color: #1a365d; }
.welcome { color: #4a5568; margin-top: 0.3rem; }
.info-banner {
  background: #fffaf0;
  border: 1px solid #fbd38d;
  border-radius: 10px;
  padding: 1rem 1.4rem;
  color: #744210;
  font-size: 0.9rem;
  line-height: 1.5;
}
</style>
