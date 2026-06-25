<template>
  <div class="sidebar">
    <div class="sidebar-header">
      <div class="logo-icon"></div>
      <span class="logo-text">IMS Portal</span>
    </div>

    <nav class="sidebar-menu">
      <Link href="/vendor/dashboard" class="menu-item" :class="{ 'active': $page.url === '/vendor/dashboard' }">
        <i class='bx bx-grid-alt'></i>
        <span>Dashboard</span>
      </Link>

      <Link href="/categories" class="menu-item" :class="{ 'active': $page.url.startsWith('/categories') }">
        <i class='bx bx-category'></i>
        <span>Categories</span>
      </Link>

      <Link href="/products" class="menu-item" :class="{ 'active': $page.url.startsWith('/products') }">
        <i class='bx bx-box'></i>
        <span>Products</span>
      </Link>

      <Link href="/orders" class="menu-item" :class="{ 'active': $page.url.startsWith('/orders') }">
        <i class='bx bx-cart'></i>
        <span>Orders</span>
      </Link>
    </nav>

    <div class="sidebar-footer">
      <Link href="/logout" method="post" as="button" class="logout-btn">
        <i class='bx bx-log-out'></i>
        <span>Logout</span>
      </Link>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.sidebar {
  width: 260px;
  height: 100vh;
  background-color: #0f0f11;
  border-right: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 100;
}

.sidebar-header {
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.logo-icon {
  width: 28px;
  height: 28px;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-radius: 6px;
}

.logo-text {
  font-size: 18px;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: -0.5px;
}

.sidebar-menu {
  flex: 1;
  padding: 24px 16px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  color: #8b8b93;
  text-decoration: none;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.menu-item i {
  font-size: 20px;
}

.menu-item:hover {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.05);
}

.menu-item.active {
  color: #ffffff;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.sidebar-footer {
  padding: 24px 16px;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.logout-btn {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: transparent;
  border: none;
  color: #ef4444;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 500;
  cursor: pointer;
  text-align: left;
  transition: all 0.2s ease;
}

.logout-btn:hover {
  background: rgba(239, 68, 68, 0.1);
}
</style>