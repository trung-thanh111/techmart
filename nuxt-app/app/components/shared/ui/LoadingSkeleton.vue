<template>
  <div class="loading-skeleton">
    <div v-if="showHeader" class="header-skeleton">
      <div class="container">
        <div class="top-bar">
          <Skeleton width="12rem" height="1rem"></Skeleton>
          <div class="flex gap-4">
            <Skeleton width="8rem" height="1rem"></Skeleton>
            <Skeleton width="6rem" height="1rem"></Skeleton>
            <Skeleton width="5rem" height="1rem"></Skeleton>
          </div>
        </div>

        <div class="main-header">
          <Skeleton width="5rem" height="2rem"></Skeleton>
          <div class="search-skeleton">
            <Skeleton height="3rem" class="flex-1"></Skeleton>
            <Skeleton width="3rem" height="3rem"></Skeleton>
          </div>
          <div class="flex gap-4">
            <Skeleton width="6rem" height="1.5rem"></Skeleton>
            <Skeleton width="5rem" height="1.5rem"></Skeleton>
            <Skeleton width="2rem" height="2rem" borderRadius="50%"></Skeleton>
          </div>
        </div>

        <div class="nav-skeleton">
          <Skeleton width="6rem" height="2.5rem"></Skeleton>
          <Skeleton width="5rem" height="2.5rem"></Skeleton>
          <Skeleton width="4rem" height="2.5rem"></Skeleton>
          <Skeleton width="5rem" height="2.5rem"></Skeleton>
          <Skeleton width="6rem" height="2.5rem" class="ml-auto"></Skeleton>
        </div>
      </div>
    </div>
    <div class="container main-content">
      <div v-if="showBanner" class="banner-skeleton">
        <Skeleton width="100%" height="20rem" borderRadius="0.75rem"></Skeleton>
      </div>
      <div class="content-grid">
        <div class="main-area">
          <div class="feature-cards">
            <Skeleton width="12rem" height="1.5rem" class="mb-4"></Skeleton>
            <div class="cards-grid">
              <div v-for="n in 3" :key="n" class="card-skeleton">
                <Skeleton width="3rem" height="3rem" borderRadius="0.75rem" class="mb-3"></Skeleton>
                <Skeleton width="8rem" height="1.25rem" class="mb-2"></Skeleton>
                <Skeleton width="100%" height="1rem" class="mb-1"></Skeleton>
                <Skeleton width="75%" height="1rem"></Skeleton>
              </div>
            </div>
          </div>
          <div class="product-section">
            <Skeleton width="14rem" height="2rem" class="mb-6"></Skeleton>
            <div class="product-grid">
              <div v-for="n in productCount" :key="n" class="product-card-skeleton">
                <Skeleton width="100%" height="12rem" borderRadius="0.75rem 0.75rem 0 0"></Skeleton>
                <div class="p-4">
                  <Skeleton width="100%" height="1rem" class="mb-2"></Skeleton>
                  <Skeleton width="75%" height="1rem" class="mb-3"></Skeleton>
                  <Skeleton width="6rem" height="1.5rem" class="mb-2"></Skeleton>
                  <Skeleton width="4rem" height="1rem"></Skeleton>
                </div>
              </div>
            </div>
          </div>
          <div class="additional-sections">
            <div v-for="section in 2" :key="section" class="section-skeleton">
              <Skeleton width="10rem" height="1.75rem" class="mb-4"></Skeleton>
              <div class="content-rows">
                <div v-for="row in 3" :key="row" class="row-skeleton">
                  <Skeleton width="4rem" height="4rem" borderRadius="0.5rem"></Skeleton>
                  <div class="flex-1">
                    <Skeleton width="75%" height="1rem" class="mb-2"></Skeleton>
                    <Skeleton width="50%" height="0.75rem"></Skeleton>
                  </div>
                  <Skeleton width="5rem" height="1.5rem"></Skeleton>
                </div>
              </div>
            </div>
          </div>
          <div class="news-section">
            <Skeleton width="8rem" height="1.75rem" class="mb-4"></Skeleton>
            <div class="news-grid">
              <div v-for="n in 4" :key="n" class="news-item">
                <Skeleton width="100%" height="8rem" borderRadius="0.5rem" class="mb-3"></Skeleton>
                <Skeleton width="100%" height="1rem" class="mb-2"></Skeleton>
                <Skeleton width="80%" height="0.875rem" class="mb-2"></Skeleton>
                <Skeleton width="4rem" height="0.75rem"></Skeleton>
              </div>
            </div>
          </div>
          <div class="category-section">
            <Skeleton width="12rem" height="1.75rem" class="mb-4"></Skeleton>
            <div class="category-grid">
              <div v-for="n in 6" :key="n" class="category-item">
                <Skeleton width="100%" height="6rem" borderRadius="0.5rem" class="mb-2"></Skeleton>
                <Skeleton width="100%" height="1rem"></Skeleton>
              </div>
            </div>
          </div>
        </div>
        <div v-if="showSidebar" class="sidebar">
          <div v-for="widget in 3" :key="widget" class="widget-skeleton">
            <Skeleton width="100%" height="16rem" borderRadius="0.75rem"></Skeleton>
          </div>
          <div class="list-widget">
            <Skeleton width="8rem" height="1.5rem" class="mb-4"></Skeleton>
            <div class="list-items">
              <div v-for="item in 5" :key="item" class="list-item">
                <Skeleton width="3rem" height="3rem" borderRadius="0.25rem"></Skeleton>
                <div class="flex-1">
                  <Skeleton width="100%" height="0.875rem" class="mb-1"></Skeleton>
                  <Skeleton width="60%" height="0.75rem"></Skeleton>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="bottom-banner">
        <Skeleton width="100%" height="8rem" borderRadius="0.75rem"></Skeleton>
      </div>
    </div>
    <div v-if="showFooter" class="footer-skeleton">
      <div class="container">
        <div class="footer-content">
          <div v-for="col in 4" :key="col" class="footer-column">
            <Skeleton width="6rem" height="1.25rem" class="mb-4"></Skeleton>
            <div v-for="link in 5" :key="link" class="mb-2">
              <Skeleton width="5rem" height="1rem"></Skeleton>
            </div>
          </div>
        </div>
        <div class="newsletter-section">
          <Skeleton width="12rem" height="1.5rem" class="mb-3"></Skeleton>
          <div class="newsletter-form">
            <Skeleton height="2.5rem" class="flex-1"></Skeleton>
            <Skeleton width="6rem" height="2.5rem"></Skeleton>
          </div>
        </div>
        <div class="footer-bottom">
          <Skeleton width="16rem" height="1rem"></Skeleton>
          <div class="social-icons">
            <Skeleton v-for="n in 4" :key="n" width="2rem" height="2rem" borderRadius="50%"></Skeleton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "LoadingSkeleton",
  props: {
    showHeader: {
      type: Boolean,
      default: true,
    },
    showBanner: {
      type: Boolean,
      default: true,
    },
    showSidebar: {
      type: Boolean,
      default: true,
    },
    showFooter: {
      type: Boolean,
      default: true,
    },
    productCount: {
      type: Number,
      default: 8,
    },
    // Tùy chỉnh animation
    animationDuration: {
      type: String,
      default: "2s",
    },
  },
  mounted() {
    // Tùy chỉnh animation duration nếu cần
    if (this.animationDuration !== "2s") {
      const skeletons = this.$el.querySelectorAll(".p-skeleton");
      skeletons.forEach((skeleton) => {
        skeleton.style.setProperty("--p-skeleton-animation-duration", this.animationDuration);
      });
    }
  },
};
</script>

<style scoped>
.loading-skeleton {
  min-height: 100vh;
  background-color: #f8f9fa;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Header Skeleton */
.header-skeleton {
  background: linear-gradient(135deg, #00b88c 0%, #077c8b 100%);
  color: white;
  padding: 12px 0;
}

.header-skeleton :deep(.p-skeleton) {
  background: linear-gradient(90deg, rgba(255, 255, 255, 0.1) 25%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.1) 75%);
  background-size: 200% 100%;
}

.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  font-size: 14px;
}

.main-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
  margin-bottom: 16px;
}

.search-skeleton {
  display: flex;
  gap: 8px;
  flex: 1;
  max-width: 500px;
}

.nav-skeleton {
  display: flex;
  align-items: center;
  gap: 32px;
}

/* Main Content */
.main-content {
  padding: 24px 20px;
}

.banner-skeleton {
  margin-bottom: 32px;
}

.content-grid {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 32px;
}

.main-area {
  min-height: 800px;
}

/* Feature Cards */
.feature-cards {
  margin-bottom: 40px;
}

.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.card-skeleton {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Product Grid */
.product-section {
  margin-bottom: 40px;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.product-card-skeleton {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Additional Sections */
.additional-sections {
  display: flex;
  flex-direction: column;
  gap: 32px;
  margin-bottom: 40px;
}

.section-skeleton {
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.content-rows {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.row-skeleton {
  display: flex;
  align-items: center;
  gap: 16px;
}

/* News Section */
.news-section {
  margin-bottom: 40px;
}

.news-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.news-item {
  background: white;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Category Section */
.category-section {
  margin-bottom: 40px;
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 16px;
}

.category-item {
  background: white;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
}

/* Sidebar */
.sidebar {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.widget-skeleton {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.list-widget {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.list-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.list-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* Bottom Banner */
.bottom-banner {
  margin: 40px 0;
}

/* Footer */
.footer-skeleton {
  background: #2c3e50;
  padding: 40px 0 20px;
  margin-top: 40px;
}

.footer-skeleton :deep(.p-skeleton) {
  background: linear-gradient(90deg, #34495e 25%, #3c4f66 50%, #34495e 75%);
  background-size: 200% 100%;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
  margin-bottom: 32px;
}

.footer-column {
  display: flex;
  flex-direction: column;
}

.newsletter-section {
  margin-bottom: 32px;
  max-width: 400px;
}

.newsletter-form {
  display: flex;
  gap: 12px;
}

.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 20px;
  border-top: 1px solid #34495e;
}

.social-icons {
  display: flex;
  gap: 12px;
}

/* Responsive */
@media (max-width: 768px) {
  .container {
    padding: 0 16px;
  }

  .content-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .main-header {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }

  .nav-skeleton {
    flex-wrap: wrap;
    gap: 16px;
  }

  .top-bar {
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
  }

  .cards-grid {
    grid-template-columns: 1fr;
  }

  .product-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }

  .news-grid {
    grid-template-columns: 1fr;
  }

  .category-grid {
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  }

  .footer-content {
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
  }

  .footer-bottom {
    flex-direction: column;
    gap: 12px;
    align-items: flex-start;
  }
}

@media (max-width: 480px) {
  .product-grid {
    grid-template-columns: 1fr;
  }

  .footer-content {
    grid-template-columns: 1fr;
  }

  .newsletter-form {
    flex-direction: column;
  }
}

/* Utility classes */
.flex {
  display: flex;
}

.gap-4 {
  gap: 16px;
}

.mb-1 {
  margin-bottom: 4px;
}
.mb-2 {
  margin-bottom: 8px;
}
.mb-3 {
  margin-bottom: 12px;
}
.mb-4 {
  margin-bottom: 16px;
}
.mb-6 {
  margin-bottom: 24px;
}

.ml-auto {
  margin-left: auto;
}
.flex-1 {
  flex: 1;
}

.p-4 {
  padding: 16px;
}
</style>
