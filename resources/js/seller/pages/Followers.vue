<template>
  <div class="followers-container">
    <div class="followers-header">
      <h2>Followers</h2>
      <p>Manage and view your shop followers</p>
    </div>

    <div class="followers-content">
      <div class="followers-stats">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fa-solid fa-users"></i>
          </div>
          <div class="stat-info">
            <h3>Total Followers</h3>
            <p class="stat-number">0</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fa-solid fa-user-plus"></i>
          </div>
          <div class="stat-info">
            <h3>New This Month</h3>
            <p class="stat-number">0</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fa-solid fa-chart-line"></i>
          </div>
          <div class="stat-info">
            <h3>Growth Rate</h3>
            <p class="stat-number">0%</p>
          </div>
        </div>
      </div>

      <div class="followers-list">
        <div class="list-header">
          <h3>Recent Followers</h3>
          <button class="refresh-btn">
            <i class="fa-solid fa-sync-alt"></i>
            Refresh
          </button>
        </div>

        <div class="empty-state">
          <div class="empty-icon">
            <i class="fa-solid fa-users-slash"></i>
          </div>
          <h4>No Followers Yet</h4>
          <p>When customers follow your shop, they'll appear here. Focus on creating great products and excellent service to attract followers!</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Followers',
  data() {
    return {
      followers: [],
      loading: false
    }
  },
  methods: {
    async loadFollowers() {
      this.loading = true;
      // TODO: Implement API call to load followers
      setTimeout(() => {
        this.loading = false;
      }, 1000);
    }
  },
  mounted() {
    this.loadFollowers();
  }
}
</script>

<style scoped>
.followers-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.followers-header {
  text-align: center;
  margin-bottom: 3rem;
}

.followers-header h2 {
  font-size: 2.5rem;
  color: #333;
  margin-bottom: 0.5rem;
  font-weight: 700;
}

.followers-header p {
  font-size: 1.1rem;
  color: #666;
  margin: 0;
}

.followers-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.followers-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1.5rem;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.stat-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #ca9d73, #c0673e);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.stat-info h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
  color: #666;
  font-weight: 500;
}

.stat-number {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
  color: #333;
}

.followers-list {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #f0f0f0;
}

.list-header h3 {
  margin: 0;
  font-size: 1.5rem;
  color: #333;
  font-weight: 600;
}

.refresh-btn {
  background: #ca9d73;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s ease;
}

.refresh-btn:hover {
  background: #b88a5f;
}

.empty-state {
  text-align: center;
  padding: 3rem 1rem;
}

.empty-icon {
  width: 80px;
  height: 80px;
  background: #f8f9fa;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  color: #ccc;
  font-size: 2rem;
}

.empty-state h4 {
  margin: 0 0 1rem 0;
  font-size: 1.3rem;
  color: #666;
  font-weight: 600;
}

.empty-state p {
  margin: 0;
  color: #999;
  line-height: 1.6;
  max-width: 400px;
  margin: 0 auto;
}

/* ===== RESPONSIVE DESIGN ===== */

/* Small mobile devices */
@media (max-width: 480px) {
  .followers-container {
    padding: 1rem;
  }

  .followers-header h2 {
    font-size: 1.8rem;
  }

  .followers-header p {
    font-size: 1rem;
  }

  .followers-stats {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .stat-card {
    padding: 1.5rem;
    gap: 1rem;
  }

  .stat-icon {
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
  }

  .stat-number {
    font-size: 1.5rem;
  }

  .followers-list {
    padding: 1.5rem;
  }

  .list-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .refresh-btn {
    width: 100%;
    justify-content: center;
  }

  .empty-state {
    padding: 2rem 0.5rem;
  }

  .empty-icon {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }
}

/* Mobile - Medium devices */
@media (max-width: 768px) {
  .followers-container {
    padding: 1.5rem;
  }

  .followers-header h2 {
    font-size: 2rem;
  }

  .followers-stats {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }

  .stat-card {
    padding: 1.5rem;
    gap: 1rem;
  }

  .stat-icon {
    width: 55px;
    height: 55px;
    font-size: 1.3rem;
  }

  .stat-number {
    font-size: 1.8rem;
  }

  .followers-list {
    padding: 1.5rem;
  }

  .list-header {
    margin-bottom: 1.5rem;
  }

  .list-header h3 {
    font-size: 1.3rem;
  }

  .refresh-btn {
    padding: 0.6rem 1.2rem;
    font-size: 0.85rem;
  }
}

/* Tablet devices */
@media (min-width: 769px) and (max-width: 1024px) {
  .followers-container {
    padding: 2rem;
  }

  .followers-header h2 {
    font-size: 2.2rem;
  }

  .followers-stats {
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
  }

  .stat-card {
    padding: 1.8rem;
    gap: 1.3rem;
  }

  .stat-icon {
    width: 58px;
    height: 58px;
    font-size: 1.4rem;
  }

  .stat-number {
    font-size: 1.9rem;
  }

  .followers-list {
    padding: 1.8rem;
  }

  .list-header h3 {
    font-size: 1.4rem;
  }

  .refresh-btn {
    padding: 0.7rem 1.4rem;
    font-size: 0.9rem;
  }
}

/* Desktop devices */
@media (min-width: 1025px) {
  .followers-container {
    padding: 2.5rem;
  }

  .followers-header h2 {
    font-size: 2.5rem;
  }

  .followers-stats {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
  }

  .stat-card {
    padding: 2rem;
    gap: 1.5rem;
  }

  .stat-icon {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }

  .stat-number {
    font-size: 2rem;
  }

  .followers-list {
    padding: 2rem;
  }

  .list-header h3 {
    font-size: 1.5rem;
  }

  .refresh-btn {
    padding: 0.75rem 1.5rem;
    font-size: 0.9rem;
  }
}

/* Large desktop devices */
@media (min-width: 1440px) {
  .followers-container {
    padding: 3rem;
  }

  .followers-header h2 {
    font-size: 3rem;
  }

  .followers-stats {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2.5rem;
  }

  .stat-card {
    padding: 2.5rem;
    gap: 2rem;
  }

  .stat-icon {
    width: 70px;
    height: 70px;
    font-size: 1.8rem;
  }

  .stat-number {
    font-size: 2.5rem;
  }

  .followers-list {
    padding: 2.5rem;
  }

  .list-header h3 {
    font-size: 1.7rem;
  }

  .refresh-btn {
    padding: 0.9rem 1.8rem;
    font-size: 1rem;
  }
}

/* Landscape orientation for mobile */
@media (max-width: 768px) and (orientation: landscape) {
  .followers-header {
    margin-bottom: 2rem;
  }

  .followers-stats {
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
  }

  .stat-card {
    padding: 1rem;
  }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .stat-card {
    border-radius: 16px;
  }

  .followers-list {
    border-radius: 16px;
  }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
  .stat-card,
  .refresh-btn {
    min-height: 44px;
    min-width: 44px;
  }
}

/* Print styles */
@media print {
  .followers-container {
    padding: 0;
  }

  .refresh-btn {
    display: none;
  }

  .stat-card,
  .followers-list {
    break-inside: avoid;
    box-shadow: none;
    border: 1px solid #ddd;
  }
}
</style>
