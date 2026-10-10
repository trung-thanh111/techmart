// Admin routes - all prefixed with /admin
export const ADMIN_PREFIX = "/admin";

// Auth routes
export const TOLOGIN = "/admin/login";
export const TOREGISTER = "/admin/register";
export const TOFORGOT = "/admin/forgot-password";
export const TOFORGOTOTP = "/admin/forgot-password/otp";
export const TOFORGOTRESET = "/admin/forgot-password/reset";

// Dashboard
export const TODASHBOARD = "/admin/dashboard";

// User module
export const USERCATALOGUEINDEX = "/admin/user/catalogue";
export const USERCATALOGUECREATE = "/admin/user/catalogue/create";
export const USERCATALOGUEUPDATE = (id: string | number) => `/admin/user/catalogue/${id}/update`;

export const ADDRESSORDERINDEX = "/admin/user/address_order";
export const ADDRESSORDERCREATE = "/admin/user/address_order/create";
export const ADDRESSORDERUPDATE = (id: string | number) => `/admin/user/address_order/${id}/update`;

export const USERINDEX = "/admin/user/user";
export const USERCREATE = "/admin/user/user/create";
export const USERUPDATE = (id: string | number) => `/admin/user/user/${id}/edit`;

export const PERMISSIONINDEX = "/admin/user/permission";
export const PERMISSIONCREATE = "/admin/user/permission/create";
export const PERMISSIONUPDATE = (id: string | number) => `/admin/user/permission/${id}/update`;

// Post module
export const POSTCATALOGUEINDEX = "/admin/post/catalogue";
export const POSTCATALOGUECREATE = "/admin/post/catalogue/create";
export const POSTCATALOGUEUPDATE = (id: string | number) => `/admin/post/catalogue/${id}/edit`;

export const POSTTAGINDEX = "/admin/post/tag";
export const POSTTAGCREATE = "/admin/post/tag/create";
export const POSTTAGUPDATE = (id: string | number) => `/admin/post/tag/${id}/edit`;

export const POSTINDEX = "/admin/post/post";
export const POSTCREATE = "/admin/post/post/create";
export const POSTUPDATE = (id: string | number) => `/admin/post/post/${id}/edit`;

// FAQ module
export const FAQINDEX = "/admin/faq/faq";
export const FAQCREATE = "/admin/faq/faq/create";
export const FAQUPDATE = (id: string | number) => `/admin/faq/faq/${id}/edit`;

export const FAQCATALOGUEINDEX = "/admin/faq/catalogue";
export const FAQCATALOGUECREATE = "/admin/faq/catalogue/create";
export const FAQCATALOGUEUPDATE = (id: string | number) => `/admin/faq/catalogue/${id}/edit`;

// Attribute module
export const ATTRIBUTEINDEX = "/admin/attribute/attribute";
export const ATTRIBUTECREATE = "/admin/attribute/attribute/create";
export const ATTRIBUTEUPDATE = (id: string | number) => `/admin/attribute/attribute/${id}/edit`;

export const ATTRIBUTECATALOGUEINDEX = "/admin/attribute/catalogue";
export const ATTRIBUTECATALOGUECREATE = "/admin/attribute/catalogue/create";
export const ATTRIBUTECATALOGUEUPDATE = (id: string | number) => `/admin/attribute/catalogue/${id}/edit`;

// Product module
export const PRODUCTINDEX = "/admin/product/product";
export const PRODUCTCREATE = "/admin/product/product/create";
export const PRODUCTUPDATE = (id: string | number) => `/admin/product/product/${id}/edit`;

export const PRODUCTCATALOGUEINDEX = "/admin/product/catalogue";
export const PRODUCTCATALOGUECREATE = "/admin/product/catalogue/create";
export const PRODUCTCATALOGUEUPDATE = (id: string | number) => `/admin/product/catalogue/${id}/edit`;

export const PRODUCTTAGINDEX = "/admin/product/tag";
export const PRODUCTTAGCREATE = "/admin/product/tag/create";
export const PRODUCTTAGUPDATE = (id: string | number) => `/admin/product/tag/${id}/edit`;

export const PRODUCTREVIEWINDEX = "/admin/product/review";

// Order module
export const ORDERINDEX = "/admin/order/order";
export const ORDERUPDATE = (id: string | number) => `/admin/order/order/${id}/edit`;

// Inventory module
export const INVENTORYINDEX = "/admin/inventory/inventory";
export const INVENTORYCREATE = "/admin/inventory/inventory/create";
export const INVENTORYUPDATE = (id: string | number) => `/admin/inventory/inventory/${id}/edit`;

// Promotion module
export const PROMOTIONCATALOGUEINDEX = "/admin/promotion/catalogue";
export const PROMOTIONCATALOGUECREATE = "/admin/promotion/catalogue/create";
export const PROMOTIONCATALOGUEUPDATE = (id: string | number) => `/admin/promotion/catalogue/${id}/edit`;

export const PROMOTIONINDEX = "/admin/promotion/promotion";
export const PROMOTIONCREATE = "/admin/promotion/promotion/create";
export const PROMOTIONUPDATE = (id: string | number) => `/admin/promotion/promotion/${id}/edit`;

// Brand module
export const BRANDINDEX = "/admin/brand/brand";
export const BRANDCREATE = "/admin/brand/brand/create";
export const BRANDUPDATE = (id: string | number) => `/admin/brand/brand/${id}/edit`;

// System module
export const SYSTEMINDEX = "/admin/system/system";
export const SYSTEMCREATE = "/admin/system/system/create";
export const SYSTEMUPDATE = (id: string | number) => `/admin/system/system/${id}/edit`;
export const POPUPINDEX = "/admin/system/popup";
export const POPUPCREATE = "/admin/system/popup/create";
export const POPUPUPDATE = (id: string | number) => `/admin/system/popup/${id}/edit`;

// Agency module
export const AGENCYINDEX = "/admin/agency/agency";
export const AGENCYCREATE = "/admin/agency/agency/create";
export const AGENCYUPDATE = (id: string | number) => `/admin/agency/agency/${id}/edit`;

export const AGENCYWORKINGHOURINDEX = "/admin/agency/working_hour";
export const AGENCYWORKINGHOURCREATE = "/admin/agency/working_hour/create";
export const AGENCYWORKINGHOURUPDATE = (id: string | number) => `/admin/agency/working_hour/${id}/edit`;

// Menu module
export const MENUINDEX = "/admin/menu/menu";
export const MENUCREATE = "/admin/menu/menu/create";
export const MENUUPDATE = (id: string | number) => `/admin/menu/menu/${id}/edit`;

// Banner module
export const BANNERINDEX = "/admin/banner/banner";
export const BANNERCREATE = "/admin/banner/banner/create";
export const BANNERUPDATE = (id: string | number) => `/admin/banner/banner/${id}/edit`;

export const BANNERPAGEINDEX = "/admin/banner/page";
export const BANNERPAGECREATE = "/admin/banner/page/create";
export const BANNERPAGEUPDATE = (id: string | number) => `/admin/banner/page/${id}/edit`;

export const BANNERPOSITIONINDEX = "/admin/banner/position";
export const BANNERPOSITIONCREATE = "/admin/banner/position/create";
export const BANNERPOSITIONUPDATE = (id: string | number) => `/admin/banner/position/${id}/edit`;

// File & Image constants
export const MAX_FILE_SIZE = 5 * 1024 * 1024;
export const ACCEPTED_IMAGE_TYPES = ["image/jpeg", "image/png", "image/webp", "image/jpg"];

// Default images
export const IMAGEDEFAULT = "/shared/images/defaults/record.jpg";
export const DEFAULTAVATAR = "/shared/images/defaults/avatar.jpg";
export const EMPTYBOX = "/client/images/box-emtpy.png";

// Home
export const TOHOME = "/admin/dashboard";

// Profile routes (if needed in admin)
export const PROFILEINDEX = "/admin/profile";
export const PROFILECHANGEPASSWORD = "/admin/profile/change-password";
export const PROFILEADDRESSORDER = "/admin/profile/address_order";
export const PROFILEORDER = "/admin/profile/order";
export const PROFILEADDRESSORDERCREATE = "/admin/profile/address_order/create";
export const PROFILEADDRESSORDERUPDATE = (id: string | number) => `/admin/profile/address_order/${id}/update`;

// Promotion Constants
export const PROMOTION_TYPES = [
    { label: "Đơn hàng", value: "order" },
    { label: "Sản phẩm", value: "product" },
    { label: "Flash Sale", value: "flash_sale" },
    { label: "Combo", value: "bundle" },
    { label: "Mua X tặng Y", value: "bxgy" },
    { label: "Miễn phí ship", value: "free_ship" },
];

export const PROMOTION_PUBLISH_STATUS = [
    { label: "Bản nháp", value: 0 },
    { label: "Riêng tư", value: 1 },
    { label: "Công khai", value: 2 },
];

export const PROMOTION_RULE_TYPES = [
    { label: "Tổng đơn tối thiểu", value: "min_order_total" },
    { label: "Số lượng sản phẩm", value: "product_qty" },
    { label: "Số lượng theo danh mục", value: "product_qty_by_category" },
    { label: "Nhóm khách hàng", value: "customer_group" },
    { label: "Khoảng thời gian", value: "time_slot" },
];

export const PROMOTION_OPERATORS = [
    { label: "=", value: "=" },
    { label: "!=", value: "!=" },
    { label: ">", value: ">" },
    { label: ">=", value: ">=" },
    { label: "<", value: "<" },
    { label: "<=", value: "<=" },
    { label: "Trong (IN)", value: "IN" },
    { label: "Không trong (NOT_IN)", value: "NOT_IN" },
    { label: "Trong khoảng (BETWEEN)", value: "BETWEEN" },
];

export const PROMOTION_ACTION_TYPES = [
    { label: "Phần trăm", value: "percent" },
    { label: "Cố định", value: "fixed" },
    { label: "Miễn phí vận chuyển", value: "free_shipping" },
    { label: "Mua X tặng Y", value: "bxgy" },
    { label: "Theo bậc", value: "tiered" },
];

export const PROMOTION_APPLY_TO = [
    { label: "Đơn hàng", value: "order" },
    { label: "Tổng giỏ hàng", value: "cart_subtotal" },
    { label: "Phí vận chuyển", value: "shipping_fee" },
    { label: "Sản phẩm rẻ nhất", value: "cheapest_item" },
    { label: "Sản phẩm đắt nhất", value: "most_expensive_item" },
    { label: "Sản phẩm cụ thể", value: "specific_products" },
];

export const PROMOTION_TARGET_TYPES = [
    { label: "Đơn hàng", value: "order" },
    { label: "Sản phẩm", value: "product" },
    { label: "Danh mục", value: "category" },
    { label: "Thương hiệu", value: "brand" },
    { label: "Nhóm khách hàng", value: "customer_group" },
];

export const PROMOTION_LOGIC_OPERATORS = [
    { label: "VÀ", value: "AND" },
    { label: "HOẶC", value: "OR" },
];
