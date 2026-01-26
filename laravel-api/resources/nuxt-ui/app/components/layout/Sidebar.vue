<template>
    <div class="app-menu navbar-menu bg-sidebar-dark" id="sidebar-admin">
        <div class="navbar-brand-box" style="margin: 10px 0">
            <NuxtLink :to="TODASHBOARD" class="logo logo-dark">
                <span class="logo-sm"><nuxt-img src="/images/logo/logo-website.png" loading="lazy" alt="logo-sm" /></span>
                <span class="logo-lg">
                    <nuxt-img src="/images/logo/logo-website.png" loading="lazy" alt="logo-lg" />
                </span>
            </NuxtLink>
            <button type="button" class="btn" style="float: right; margin-top: 4px">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>
        <div id="scrollbar" data-simplebar style="max-height: 100%; width: 100%">
            <div class="container-fluid px-0">
                <ul class="navbar-nav" id="navbar-nav">
                    <li class="menu-title">
                        <span class="text-light">{{ $t("admin.sidebar.statistics") }}</span>
                    </li>
                    <li class="nav-item">
                        <NuxtLink class="nav-link menu-link" :to="TODASHBOARD">
                            <i class="ri-dashboard-2-line"></i>
                            <span data-key="t-dashboards">{{ $t("admin.sidebar.dashboard") }}</span>
                        </NuxtLink>
                    </li>

                    <li class="menu-title">
                        <i class="ri-more-fill"></i>
                        <span class="text-light">{{ $t("admin.sidebar.management") }}</span>
                    </li>

                    <li class="nav-item">
                        <NuxtLink class="nav-link menu-link" to="#sidebarProducts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProducts">
                            <i class="ri-shopping-bag-3-line"></i>
                            <span data-key="t-product-pages">{{ $t("admin.sidebar.products") }}</span>
                        </NuxtLink>
                        <div class="collapse menu-dropdown" id="sidebarProducts">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <NuxtLink :to="PRODUCTCATALOGUEINDEX" class="nav-link" data-key="t-product-catelogue">{{ $t("admin.sidebar.productCatalogue") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="INVENTORYINDEX" class="nav-link" data-key="t-product-inventory">{{ $t("admin.sidebar.inventory") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink to="#sidebarProduct" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProduct" data-key="t-profile">{{ $t("admin.sidebar.products") }}</NuxtLink>
                                    <div class="collapse menu-dropdown" id="sidebarProduct">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <NuxtLink :to="PRODUCTINDEX" class="nav-link" data-key="t-product-list">{{ $t("admin.sidebar.productList") }}</NuxtLink>
                                            </li>
                                            <li class="nav-item">
                                                <NuxtLink :to="PRODUCTCREATE" class="nav-link" data-key="t-product-create">{{ $t("admin.sidebar.productCreate") }}</NuxtLink>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink to="#sidebarAttribute" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAttribute" data-key="t-profile">{{ $t("admin.sidebar.attributes") }}</NuxtLink>
                                    <div class="collapse menu-dropdown" id="sidebarAttribute">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <NuxtLink :to="ATTRIBUTECATALOGUEINDEX" class="nav-link" data-key="t-attribute-catelogue">{{ $t("admin.sidebar.attributeCatalogue") }}</NuxtLink>
                                            </li>
                                            <li class="nav-item">
                                                <NuxtLink :to="ATTRIBUTEINDEX" class="nav-link" data-key="t-attribute-list">{{ $t("admin.sidebar.attributeList") }}</NuxtLink>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="BRANDINDEX" class="nav-link" data-key="t-brand-list">{{ $t("admin.sidebar.brands") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="PRODUCTTAGINDEX" class="nav-link" data-key="t-product-list">{{ $t("admin.sidebar.tags") }}</NuxtLink>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <NuxtLink class="nav-link menu-link" to="#sidebarPromotions" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPromotions">
                            <i class="ri-price-tag-3-line"></i>
                            <span data-key="t-promotion-pages">{{ $t("admin.sidebar.promotions") }}</span>
                        </NuxtLink>
                        <div class="collapse menu-dropdown" id="sidebarPromotions">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <NuxtLink :to="PROMOTIONCATALOGUEINDEX" class="nav-link" data-key="t-promotion-catelogue">{{ $t("admin.sidebar.promotionCatalogue") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink to="#sidebarPromotion" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPromotion" data-key="t-profile">{{ $t("admin.sidebar.promotions") }}</NuxtLink>
                                    <div class="collapse menu-dropdown" id="sidebarPromotion">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <NuxtLink :to="PROMOTIONINDEX" class="nav-link" data-key="t-promotion-list">{{ $t("admin.sidebar.promotionList") }}</NuxtLink>
                                            </li>
                                            <li class="nav-item">
                                                <NuxtLink :to="PROMOTIONCREATE" class="nav-link" data-key="t-promotion-create">{{ $t("admin.sidebar.promotionCreate") }}</NuxtLink>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <NuxtLink class="nav-link menu-link" to="#sidebarusers" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarusers">
                            <i class="ri-group-line"></i>
                            <span data-key="t-user-pages">{{ $t("admin.sidebar.customers") }}</span>
                        </NuxtLink>
                        <div class="collapse menu-dropdown" id="sidebarusers">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <NuxtLink to="#sidebaruser" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebaruser" data-key="t-user">{{ $t("admin.sidebar.customers") }}</NuxtLink>
                                    <div class="collapse menu-dropdown" id="sidebaruser">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <NuxtLink :to="USERINDEX" class="nav-link" data-key="t-user-list">{{ $t("admin.sidebar.customerList") }}</NuxtLink>
                                            </li>
                                            <li class="nav-item">
                                                <NuxtLink :to="USERCREATE" class="nav-link" data-key="t-user-create">{{ $t("admin.sidebar.customerCreate") }}</NuxtLink>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="USERCATALOGUEINDEX" class="nav-link" data-key="t-user-catelogue">{{ $t("admin.sidebar.customerCatalogue") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="PERMISSIONINDEX" class="nav-link" data-key="t-user-permission">{{ $t("admin.sidebar.permission") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="ADDRESSORDERINDEX" class="nav-link" data-key="t-user-address-order">{{ $t("admin.sidebar.addressOrder") }}</NuxtLink>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <NuxtLink class="nav-link menu-link" to="#sidebarposts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarposts">
                            <i class="ri-news-line"></i>
                            <span data-key="t-post-pages">{{ $t("admin.sidebar.posts") }}</span>
                        </NuxtLink>
                        <div class="collapse menu-dropdown" id="sidebarposts">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <NuxtLink to="#sidebarpost" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarpost" data-key="t-post">{{ $t("admin.sidebar.posts") }}</NuxtLink>
                                    <div class="collapse menu-dropdown" id="sidebarpost">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <NuxtLink :to="POSTINDEX" class="nav-link" data-key="t-post-list">{{ $t("admin.sidebar.postList") }}</NuxtLink>
                                            </li>
                                            <li class="nav-item">
                                                <NuxtLink :to="POSTCREATE" class="nav-link" data-key="t-post-create">{{ $t("admin.sidebar.postCreate") }}</NuxtLink>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="POSTCATALOGUEINDEX" class="nav-link" data-key="t-post-catelogue">{{ $t("admin.sidebar.postCatalogue") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="POSTTAGINDEX" class="nav-link" data-key="t-post-tag">{{ $t("admin.sidebar.tags") }}</NuxtLink>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <NuxtLink class="nav-link menu-link" to="#sidebarfaqs" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarfaqs">
                            <i class="ri-questionnaire-line"></i>
                            <span data-key="t-faq-pages">{{ $t("admin.sidebar.faqs") }}</span>
                        </NuxtLink>
                        <div class="collapse menu-dropdown" id="sidebarfaqs">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <NuxtLink :to="FAQINDEX" class="nav-link" data-key="t-faq-list">{{ $t("admin.sidebar.faqList") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="FAQCREATE" class="nav-link" data-key="t-faq-create">{{ $t("admin.sidebar.faqCreate") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="FAQCATALOGUEINDEX" class="nav-link" data-key="t-faq-catelogue">{{ $t("admin.sidebar.faqCatalogue") }}</NuxtLink>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <NuxtLink class="nav-link menu-link" to="#sidebaragencys" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebaragencys">
                            <i class="ri-store-2-line"></i>
                            <span data-key="t-agency-pages">{{ $t("admin.sidebar.agencys") }}</span>
                        </NuxtLink>
                        <div class="collapse menu-dropdown" id="sidebaragencys">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <NuxtLink :to="AGENCYINDEX" class="nav-link" data-key="t-agency-list">{{ $t("admin.sidebar.agencyList") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="AGENCYCREATE" class="nav-link" data-key="t-agency-create">{{ $t("admin.sidebar.agencyCreate") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="AGENCYWORKINGHOURINDEX" class="nav-link" data-key="t-agency-catelogue">{{ $t("admin.sidebar.agencyWorkingHour") }}</NuxtLink>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <NuxtLink class="nav-link menu-link" to="#sidebarbanners" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarbanners">
                            <i class="ri-image-2-line"></i>
                            <span data-key="t-banner-pages">{{ $t("admin.sidebar.banners") }}</span>
                        </NuxtLink>
                        <div class="collapse menu-dropdown" id="sidebarbanners">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <NuxtLink to="#sidebarbanner" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarbanner" data-key="t-banner">{{ $t("admin.sidebar.banners") }}</NuxtLink>
                                    <div class="collapse menu-dropdown" id="sidebarbanner">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <NuxtLink :to="BANNERINDEX" class="nav-link" data-key="t-banner-list">{{ $t("admin.sidebar.bannerList") }}</NuxtLink>
                                            </li>
                                            <li class="nav-item">
                                                <NuxtLink :to="BANNERCREATE" class="nav-link" data-key="t-banner-create">{{ $t("admin.sidebar.bannerCreate") }}</NuxtLink>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="BANNERPAGEINDEX" class="nav-link" data-key="t-banner-page">{{ $t("admin.sidebar.bannerPage") }}</NuxtLink>
                                </li>
                                <li class="nav-item">
                                    <NuxtLink :to="BANNERPOSITIONINDEX" class="nav-link" data-key="t-banner-position">{{ $t("admin.sidebar.bannerPosition") }}</NuxtLink>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="menu-title">
                        <i class="ri-more-fill"></i>
                        <span class="text-light">{{ $t("admin.sidebar.configuration") }}</span>
                    </li>
                    <li class="nav-item">
                        <NuxtLink :to="SYSTEMINDEX" class="nav-link menu-link">
                            <i class="ri-settings-3-line"></i>
                            <span data-key="t-configuration">{{ $t("admin.sidebar.systemList") }}</span>
                        </NuxtLink>
                        <NuxtLink :to="POPUPINDEX" class="nav-link menu-link">
                            <i class="ri-notification-line"></i>
                            <span data-key="t-popup-list">{{ $t("admin.sidebar.popupList") }}</span>
                        </NuxtLink>
                        <NuxtLink :to="MENUINDEX" class="nav-link menu-link">
                            <i class="ri-menu-line"></i>
                            <span data-key="t-menu-list">{{ $t("admin.sidebar.menuList") }}</span>
                        </NuxtLink>
                    </li>
                </ul>
            </div>
        </div>

        <div class="sidebar-background"></div>
    </div>
    <div class="vertical-overlay"></div>
</template>
<script setup lang="ts">
import { TODASHBOARD, PRODUCTCATALOGUEINDEX, PRODUCTINDEX, PRODUCTCREATE, ATTRIBUTECATALOGUEINDEX, ATTRIBUTEINDEX, BRANDINDEX, PRODUCTTAGINDEX, PROMOTIONCATALOGUEINDEX, USERINDEX, USERCREATE, USERCATALOGUEINDEX, ADDRESSORDERINDEX, POSTINDEX, POSTCREATE, POSTCATALOGUEINDEX, POSTTAGINDEX, FAQINDEX, FAQCREATE, FAQCATALOGUEINDEX, AGENCYINDEX, AGENCYCREATE, AGENCYWORKINGHOURINDEX, BANNERINDEX, BANNERCREATE, BANNERPAGEINDEX, BANNERPOSITIONINDEX, SYSTEMINDEX, POPUPINDEX, MENUINDEX } from "~/utils/constants";
</script>
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
