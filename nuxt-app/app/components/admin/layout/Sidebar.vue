<template>
  <div class="app-menu navbar-menu bg-sidebar-dark" id="sidebar-admin">
    <div class="navbar-brand-box" style="margin: 10px 0">
      <NuxtLink to="/admin/dashboard" class="logo logo-dark">
        <span class="logo-sm"><nuxt-img src="/shared/images/logo/logo-website.png" loading="lazy" alt="logo-sm" /></span>
        <span class="logo-lg">
          <nuxt-img src="/shared/images/logo/logo-website.png" loading="lazy" alt="logo-lg" />
        </span>
      </NuxtLink>
      <button type="button" class="btn" style="float: right; margin-top: 4px">
        <i class="ri-record-circle-line"></i>
      </button>
    </div>
    <div id="scrollbar" data-simplebar style="max-height: 100%; width: 100%;">
      <div class="container-fluid px-0">
        <ul class="navbar-nav" id="navbar-nav">
          <li class="menu-title">
            <span class="text-light">{{ $t("admin.sidebar.statistics") }}</span>
          </li>
          <li class="nav-item">
            <NuxtLink class="nav-link menu-link" to="/admin/dashboard">
              <i class="ri-dashboard-2-line"></i>
              <span data-key="t-dashboards">{{ $t("admin.sidebar.dashboard") }}</span>
            </NuxtLink>
          </li>
          <li class="menu-title">
            <i class="ri-more-fill"></i>
            <span class="text-light">{{ $t("admin.sidebar.management") }}</span>
          </li>
          <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarProducts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProducts">
              <i class="ri-pages-line"></i>
              <span data-key="t-product-pages">{{ $t("admin.sidebar.products") }}</span>
            </a>
            <div class="collapse menu-dropdown" id="sidebarProducts">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="#" class="nav-link" data-key="t-product-catelogue">{{ $t("admin.sidebar.productCatalogue") }}</a>
                </li>
                <li class="nav-item">
                  <a href="#sidebarProduct" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProduct" data-key="t-profile">{{ $t("admin.sidebar.products") }}</a>
                  <div class="collapse menu-dropdown" id="sidebarProduct">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">
                        <a href="#" class="nav-link" data-key="t-product-list">{{ $t("admin.sidebar.productList") }}</a>
                      </li>
                      <li class="nav-item">
                        <a href="#" class="nav-link" data-key="t-product-create">{{ $t("admin.sidebar.productCreate") }}</a>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                  <a href="#sidebarAttribute" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAttribute" data-key="t-profile">{{ $t("admin.sidebar.attributes") }}</a>
                  <div class="collapse menu-dropdown" id="sidebarAttribute">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">
                        <a href="#" class="nav-link" data-key="t-attribute-catalogue">{{ $t("admin.sidebar.attributeCatalogue") }}</a>
                      </li>
                      <li class="nav-item">
                        <a href="#" class="nav-link" data-key="t-attribute-list">{{ $t("admin.sidebar.attributeList") }}</a>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link" data-key="t-product-brand">{{ $t("admin.sidebar.brands") }}</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link" data-key="t-product-attribute">{{ $t("admin.sidebar.tags") }}</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarusers" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarusers">
              <i class="ri-group-line"></i>
              <span data-key="t-user-pages">{{ $t("admin.sidebar.customers") }}</span>
            </a>
            <div class="collapse menu-dropdown" id="sidebarusers">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="#sidebaruser" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebaruser" data-key="t-user">{{ $t("admin.sidebar.customers") }}</a>
                  <div class="collapse menu-dropdown" id="sidebaruser">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">
                        <NuxtLink to="/admin/user/user" class="nav-link" data-key="t-user-list">{{ $t("admin.sidebar.customerList") }}</NuxtLink>
                      </li>
                      <li class="nav-item">
                        <NuxtLink to="/admin/user/user/create" class="nav-link" data-key="t-user-create">{{ $t("admin.sidebar.customerCreate") }}</NuxtLink>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                  <NuxtLink to="/admin/user/catalogue" class="nav-link" data-key="t-user-catelogue">{{ $t("admin.sidebar.customerCatalogue") }}</NuxtLink>
                </li>
                <li class="nav-item">
                  <NuxtLink to="/admin/user/address_order" class="nav-link" data-key="t-user-address-order">{{ $t("admin.sidebar.addressOrder") }}</NuxtLink>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarposts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarposts">
              <i class="ri-news-line"></i>
              <span data-key="t-post-pages">{{ $t("admin.sidebar.posts") }}</span>
            </a>
            <div class="collapse menu-dropdown" id="sidebarposts">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="#sidebarpost" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarpost" data-key="t-post">{{ $t("admin.sidebar.posts") }}</a>
                  <div class="collapse menu-dropdown" id="sidebarpost">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">
                        <NuxtLink to="/admin/post/post" class="nav-link" data-key="t-post-list">{{ $t("admin.sidebar.postList") }}</NuxtLink>
                      </li>
                      <li class="nav-item">
                        <NuxtLink to="/admin/post/post/create" class="nav-link" data-key="t-post-create">{{ $t("admin.sidebar.postCreate") }}</NuxtLink>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                  <NuxtLink to="/admin/post/catalogue" class="nav-link" data-key="t-post-catelogue">{{ $t("admin.sidebar.postCatalogue") }}</NuxtLink>
                </li>
                <li class="nav-item">
                  <NuxtLink to="/admin/post/tag" class="nav-link" data-key="t-post-tag">{{ $t("admin.sidebar.tags") }}</NuxtLink>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarfaqs" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarfaqs">
              <i class="ri-news-line"></i>
              <span data-key="t-faq-pages">{{ $t("admin.sidebar.faqs") }}</span>
            </a>
            <div class="collapse menu-dropdown" id="sidebarfaqs">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="#sidebarfaq" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarfaq" data-key="t-faq">{{ $t("admin.sidebar.faqs") }}</a>
                  <div class="collapse menu-dropdown" id="sidebarfaq">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">
                        <NuxtLink to="/admin/faq/faq" class="nav-link" data-key="t-faq-list">{{ $t("admin.sidebar.faqList") }}</NuxtLink>
                      </li>
                      <li class="nav-item">
                        <NuxtLink to="/admin/faq/faq/create" class="nav-link" data-key="t-faq-create">{{ $t("admin.sidebar.faqCreate") }}</NuxtLink>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                  <NuxtLink to="/admin/faq/catalogue" class="nav-link" data-key="t-faq-catelogue">{{ $t("admin.sidebar.faqCatalogue") }}</NuxtLink>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarattributes" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarattributes">
              <i class="ri-news-line"></i>
              <span data-key="t-attribute-pages">{{ $t("admin.sidebar.attributes") }}</span>
            </a>
            <div class="collapse menu-dropdown" id="sidebarattributes">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="#sidebarattribute" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarattribute" data-key="t-attribute">{{ $t("admin.sidebar.attributes") }}</a>
                  <div class="collapse menu-dropdown" id="sidebarattribute">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">
                        <NuxtLink to="/admin/attribute/attribute" class="nav-link" data-key="t-attribute-list">{{ $t("admin.sidebar.attributeList") }}</NuxtLink>
                      </li>
                      <li class="nav-item">
                        <NuxtLink to="/admin/attribute/attribute/create" class="nav-link" data-key="t-attribute-create">{{ $t("admin.sidebar.attributeCreate") }}</NuxtLink>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                  <NuxtLink to="/admin/attribute/catalogue" class="nav-link" data-key="t-attribute-catelogue">{{ $t("admin.sidebar.attributeCatalogue") }}</NuxtLink>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebaragencys" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebaragencys">
              <i class="ri-news-line"></i>
              <span data-key="t-agency-pages">{{ $t("admin.sidebar.agencys") }}</span>
            </a>
            <div class="collapse menu-dropdown" id="sidebaragencys">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="#sidebaragency" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebaragency" data-key="t-agency">{{ $t("admin.sidebar.agencys") }}</a>
                  <div class="collapse menu-dropdown" id="sidebaragency">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">
                        <NuxtLink to="/admin/agency/agency" class="nav-link" data-key="t-agency-list">{{ $t("admin.sidebar.agencyList") }}</NuxtLink>
                      </li>
                      <li class="nav-item">
                        <NuxtLink to="/admin/agency/agency/create" class="nav-link" data-key="t-agency-create">{{ $t("admin.sidebar.agencyCreate") }}</NuxtLink>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                  <NuxtLink to="/admin/agency/working_hour" class="nav-link" data-key="t-agency-catelogue">{{ $t("admin.sidebar.agencyWorkingHour") }}</NuxtLink>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarbrands" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarbrands">
              <i class="ri-news-line"></i>
              <span data-key="t-brand-pages">{{ $t("admin.sidebar.brands") }}</span>
            </a>
            <div class="collapse menu-dropdown" id="sidebarbrands">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="#sidebarbrand" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarbrand" data-key="t-brand">{{ $t("admin.sidebar.brands") }}</a>
                  <div class="collapse menu-dropdown" id="sidebarbrand">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">
                        <NuxtLink to="/admin/brand/brand" class="nav-link" data-key="t-brand-list">{{ $t("admin.sidebar.brandList") }}</NuxtLink>
                      </li>
                      <li class="nav-item">
                        <NuxtLink to="/admin/brand/brand/create" class="nav-link" data-key="t-brand-create">{{ $t("admin.sidebar.brandCreate") }}</NuxtLink>
                      </li>
                    </ul>
                  </div>
                </li>
              </ul>
            </div>
          </li>
          <li class="menu-title">
            <i class="ri-more-fill"></i>
            <span class="text-light">{{ $t("admin.sidebar.configuration") }}</span>
          </li>
          <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarUI" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUI">
              <i class="ri-pencil-ruler-2-line"></i>
              <span data-key="t-base-ui">{{ $t("admin.sidebar.interface") }}</span>
            </a>
            <div class="collapse menu-dropdown mega-dropdown-menu" id="sidebarUI">
              <div class="row">
                <div class="col-lg-4">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                      <a href="ui-alerts.html" class="nav-link" data-key="t-alerts">{{ $t("admin.sidebar.alerts") }}</a>
                    </li>
                    <li class="nav-item">
                      <a href="ui-badges.html" class="nav-link" data-key="t-badges">{{ $t("admin.sidebar.badges") }}</a>
                    </li>
                    <li class="nav-item">
                      <a href="ui-buttons.html" class="nav-link" data-key="t-buttons">{{ $t("admin.sidebar.buttons") }}</a>
                    </li>
                    <li class="nav-item">
                      <a href="ui-colors.html" class="nav-link" data-key="t-colors">{{ $t("admin.sidebar.colors") }}</a>
                    </li>
                    <li class="nav-item">
                      <a href="ui-cards.html" class="nav-link" data-key="t-cards">{{ $t("admin.sidebar.cards") }}</a>
                    </li>
                    <li class="nav-item">
                      <a href="ui-carousel.html" class="nav-link" data-key="t-carousel">{{ $t("admin.sidebar.carousel") }}</a>
                    </li>
                    <li class="nav-item">
                      <a href="ui-dropdowns.html" class="nav-link" data-key="t-dropdowns">{{ $t("admin.sidebar.dropdowns") }}</a>
                    </li>
                    <li class="nav-item">
                      <a href="ui-grid.html" class="nav-link" data-key="t-grid">{{ $t("admin.sidebar.grid") }}</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div class="sidebar-background"></div>
  </div>
  <div class="vertical-overlay"></div>
</template>
<script setup></script>
<style scoped>
.collapse {
  visibility: visible !important;
}
.bg-sidebar-dark {
  background-color: #1c233c;
}
.logo {
  margin: 24px 0 !important;
}
.navbar-nav {
  width: 100%;
}
</style>
