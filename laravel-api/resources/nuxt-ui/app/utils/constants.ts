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
export const USERCATALOGUEUPDATE = "/admin/user/catalogue/update";

export const ADDRESSORDERINDEX = "/admin/user/address_order";
export const ADDRESSORDERCREATE = "/admin/user/address_order/create";
export const ADDRESSORDERUPDATE = "/admin/user/address_order/update";

export const USERINDEX = "/admin/user/user";
export const USERCREATE = "/admin/user/user/create";
export const USERUPDATE = "/admin/user/user/update";

export const PERMISSIONINDEX = "/admin/user/permission";
export const PERMISSIONCREATE = "/admin/user/permission/create";
export const PERMISSIONUPDATE = "/admin/user/permission/update";

// Post module
export const POSTCATALOGUEINDEX = "/admin/post/catalogue";
export const POSTCATALOGUECREATE = "/admin/post/catalogue/create";
export const POSTCATALOGUEUPDATE = "/admin/post/catalogue/update";

export const POSTTAGINDEX = "/admin/post/tag";
export const POSTTAGCREATE = "/admin/post/tag/create";
export const POSTTAGUPDATE = "/admin/post/tag/update";

export const POSTINDEX = "/admin/post/post";
export const POSTCREATE = "/admin/post/post/create";
export const POSTUPDATE = "/admin/post/post/update";

// FAQ module
export const FAQINDEX = "/admin/faq/faq";
export const FAQCREATE = "/admin/faq/faq/create";
export const FAQUPDATE = "/admin/faq/faq/update";

export const FAQCATALOGUEINDEX = "/admin/faq/catalogue";
export const FAQCATALOGUECREATE = "/admin/faq/catalogue/create";
export const FAQCATALOGUEUPDATE = "/admin/faq/catalogue/update";

// Attribute module
export const ATTRIBUTEINDEX = "/admin/attribute/attribute";
export const ATTRIBUTECREATE = "/admin/attribute/attribute/create";
export const ATTRIBUTEUPDATE = "/admin/attribute/attribute/update";

export const ATTRIBUTECATALOGUEINDEX = "/admin/attribute/catalogue";
export const ATTRIBUTECATALOGUECREATE = "/admin/attribute/catalogue/create";
export const ATTRIBUTECATALOGUEUPDATE = "/admin/attribute/catalogue/update";

// Product module
export const PRODUCTINDEX = "/admin/product/product";
export const PRODUCTCREATE = "/admin/product/product/create";
export const PRODUCTUPDATE = "/admin/product/product/update";

export const PRODUCTCATALOGUEINDEX = "/admin/product/catalogue";
export const PRODUCTCATALOGUECREATE = "/admin/product/catalogue/create";
export const PRODUCTCATALOGUEUPDATE = "/admin/product/catalogue/update";

export const PRODUCTTAGINDEX = "/admin/product/tag";
export const PRODUCTTAGCREATE = "/admin/product/tag/create";
export const PRODUCTTAGUPDATE = "/admin/product/tag/update";

// Order module
export const ORDERINDEX = "/admin/order/order";

// Inventory module
export const INVENTORYINDEX = "/admin/inventory/inventory";
export const INVENTORYCREATE = "/admin/inventory/inventory/create";
export const INVENTORYUPDATE = "/admin/inventory/inventory/update";

// Promotion module
export const PROMOTIONCATALOGUEINDEX = "/admin/promotion/catalogue";
export const PROMOTIONCATALOGUECREATE = "/admin/promotion/catalogue/create";
export const PROMOTIONCATALOGUEUPDATE = "/admin/promotion/catalogue/update";

export const PROMOTIONINDEX = "/admin/promotion/promotion";
export const PROMOTIONCREATE = "/admin/promotion/promotion/create";
export const PROMOTIONUPDATE = "/admin/promotion/promotion/update";

// Brand module
export const BRANDINDEX = "/admin/brand/brand";
export const BRANDCREATE = "/admin/brand/brand/create";
export const BRANDUPDATE = "/admin/brand/brand/update";

// System module
export const SYSTEMINDEX = "/admin/system/system";
export const SYSTEMCREATE = "/admin/system/system/create";
export const SYSTEMUPDATE = "/admin/system/system/update";
export const POPUPINDEX = "/admin/system/popup";
export const POPUPCREATE = "/admin/system/popup/create";

// Agency module
export const AGENCYINDEX = "/admin/agency/agency";
export const AGENCYCREATE = "/admin/agency/agency/create";
export const AGENCYUPDATE = "/admin/agency/agency/update";

export const AGENCYWORKINGHOURINDEX = "/admin/agency/working_hour";
export const AGENCYWORKINGHOURCREATE = "/admin/agency/working_hour/create";
export const AGENCYWORKINGHOURUPDATE = "/admin/agency/working_hour/update";

// Menu module
export const MENUINDEX = "/admin/menu/menu";
export const MENUCREATE = "/admin/menu/menu/create";
export const MENUUPDATE = "/admin/menu/menu/update";

// Banner module
export const BANNERINDEX = "/admin/banner/banner";
export const BANNERCREATE = "/admin/banner/banner/create";
export const BANNERUPDATE = "/admin/banner/banner/update";

export const BANNERPAGEINDEX = "/admin/banner/page";
export const BANNERPAGECREATE = "/admin/banner/page/create";
export const BANNERPAGEUPDATE = "/admin/banner/page/update";

export const BANNERPOSITIONINDEX = "/admin/banner/position";
export const BANNERPOSITIONCREATE = "/admin/banner/position/create";
export const BANNERPOSITIONUPDATE = "/admin/banner/position/update";

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
