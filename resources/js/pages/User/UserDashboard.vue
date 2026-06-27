<template>
  <div class="dashboard-layout">
    <Usernavbar />

    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Dashboard Overview</h1>
          <p>Welcome back to your portal summary.</p>
        </div>
      </header>

      <section class="dashboard-grid">
        <div class="stat-card">
          <div class="card-icon blue">
              <i class='bx bx-category'></i>
          </div>
          <div class="card-info">
              <h3>Categories</h3>
              <p class="stat-value">{{ categories }}</p>
          </div>
      </div>

      <div class="stat-card">
          <div class="card-icon purple">
              <i class='bx bx-box'></i>
          </div>
          <div class="card-info">
              <h3>Active Products</h3>
              <p class="stat-value">{{ products }}</p>
          </div>
      </div>

        <div class="stat-card">
          <div class="card-icon green"><i class='bx bx-cart'></i></div>
          <div class="card-info">
            <h3>Total Orders</h3>
            <p class="stat-value">0</p>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
// Importing layout companion element smoothly
import Usernavbar from './Usernavbar.vue';
defineProps({
  products: Number,
  categories: Number,
});

</script>

<style scoped>
.dashboard-layout {
  min-height: 100vh;
  background-color: #000000;
  color: #ffffff;
  font-family: 'Inter', sans-serif;
}

.main-content {
  margin-left: 260px; /* Shifts everything to make room for fixed sidebar width */
  padding: 40px;
}

.content-header {
  margin-bottom: 32px;
}

.content-header h1 {
  font-size: 28px;
  font-weight: 700;
  letter-spacing: -0.5px;
  margin-bottom: 6px;
}

.content-header p {
  color: #8b8b93;
  font-size: 15px;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}

.stat-card {
  background: rgba(20, 20, 22, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 20px;
}

.card-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}

.card-icon.blue { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
.card-icon.purple { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }
.card-icon.green { background: rgba(16, 185, 129, 0.15); color: #10b981; }

.card-info h3 {
  font-size: 14px;
  font-weight: 500;
  color: #8b8b93;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #ffffff;
}
</style>