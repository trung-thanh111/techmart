// admin route
export const TODASHBOARD = "/dashboard";

export const USERCATALOGUEINDEX = "/user/catalogue";
export const USERCATALOGUECREATE = "/user/catalogue/create";
export const USERCATALOGUEUPDATE = "/user/catalogue/update";

export const ADDRESSORDERINDEX = "/user/address_order";
export const ADDRESSORDERCREATE = "/user/address_order/create";
export const ADDRESSORDERUPDATE = "/user/address_order/update";

export const USERINDEX = "/user/user";
export const USERCREATE = "/user/user/create";
export const USERUPDATE = "/user/user/update";

export const PERMISSIONINDEX = "/user/permission";
export const PERMISSIONCREATE = "/user/permission/create";
export const PERMISSIONUPDATE = "/user/permission/update";

export const POSTCATALOGUEINDEX = "/post/catalogue";
export const POSTCATALOGUECREATE = "/post/catalogue/create";
export const POSTCATALOGUEUPDATE = "/post/catalogue/update";

export const POSTTAGINDEX = "/post/tag";
export const POSTTAGCREATE = "/post/tag/create";
export const POSTTAGUPDATE = "/post/tag/update";

export const POSTINDEX = "/post/post";
export const POSTCREATE = "/post/post/create";
export const POSTUPDATE = "/post/post/update";

export const FAQINDEX = "/faq/faq";
export const FAQCREATE = "/faq/faq/create";
export const FAQUPDATE = "/faq/faq/update";

export const FAQCATALOGUEINDEX = "/faq/catalogue";
export const FAQCATALOGUECREATE = "/faq/catalogue/create";
export const FAQCATALOGUEUPDATE = "/faq/catalogue/update";

export const ATTRIBUTEINDEX = "/attribute/attribute";
export const ATTRIBUTECREATE = "/attribute/attribute/create";
export const ATTRIBUTEUPDATE = "/attribute/attribute/update";

export const ATTRIBUTECATALOGUEINDEX = "/attribute/catalogue";
export const ATTRIBUTECATALOGUECREATE = "/attribute/catalogue/create";
export const ATTRIBUTECATALOGUEUPDATE = "/attribute/catalogue/update";

export const PRODUCTINDEX = "/product/product";
export const PRODUCTCREATE = "/product/product/create";
export const PRODUCTUPDATE = "/product/product/update";

export const INVENTORYINDEX = "/inventory/inventory";
export const INVENTORYCREATE = "/inventory/inventory/create";
export const INVENTORYUPDATE = "/inventory/inventory/update";

export const PRODUCTCATALOGUEINDEX = "/product/catalogue";
export const PRODUCTCATALOGUECREATE = "/product/catalogue/create";
export const PRODUCTCATALOGUEUPDATE = "/product/catalogue/update";

export const PROMOTIONCATALOGUEINDEX = "/promotion/catalogue";
export const PROMOTIONCATALOGUECREATE = "/promotion/catalogue/create";
export const PROMOTIONCATALOGUEUPDATE = "/promotion/catalogue/update";

export const PROMOTIONINDEX = "/promotion/promotion";
export const PROMOTIONCREATE = "/promotion/promotion/create";
export const PROMOTIONUPDATE = "/promotion/promotion/update";

export const PRODUCTTAGINDEX = "/product/tag";
export const PRODUCTTAGCREATE = "/product/tag/create";
export const PRODUCTTAGUPDATE = "/product/tag/update";

export const BRANDINDEX = "/brand/brand";
export const BRANDCREATE = "/brand/brand/create";
export const BRANDUPDATE = "/brand/brand/update";

export const SYSTEMINDEX = "/system/system";
export const SYSTEMCREATE = "/system/system/create";
export const POPUPINDEX = "/system/popup";
export const POPUPCREATE = "/system/popup/create";
export const SYSTEMUPDATE = "/system/system/update";

export const AGENCYINDEX = "/agency/agency";
export const AGENCYCREATE = "/agency/agency/create";
export const AGENCYUPDATE = "/agency/agency/update";

export const MENUINDEX = "/menu/menu";
export const MENUCREATE = "/menu/menu/create";
export const MENUUPDATE = "/menu/menu/update";

export const BANNERINDEX = "/banner/banner";
export const BANNERCREATE = "/banner/banner/create";
export const BANNERUPDATE = "/banner/banner/update";

export const BANNERPAGEINDEX = "/banner/page";
export const BANNERPAGECREATE = "/banner/page/create";
export const BANNERPAGEUPDATE = "/banner/page/update";

export const BANNERPOSITIONINDEX = "/banner/position";
export const BANNERPOSITIONCREATE = "/banner/position/create";
export const BANNERPOSITIONUPDATE = "/banner/position/update";

export const AGENCYWORKINGHOURINDEX = "/agency/working_hour";
export const AGENCYWORKINGHOURCREATE = "/agency/working_hour/create";
export const AGENCYWORKINGHOURUPDATE = "/agency/working_hour/update";

export const MAX_FILE_SIZE = 5 * 1024 * 1024;
export const ACCEPTED_IMAGE_TYPES = ["image/jpeg", "image/png", "image/webp", "image/jpg"];

// auth
export const TOLOGIN = "/auth/login";
export const TOREGISTER = "/auth/register";
// forgot password (client)
export const TOFORGOT = "/forgot-password";
export const TOFORGOTOTP = "/forgot-password/otp";
export const TOFORGOTRESET = "/forgot-password/reset";

// FE
export const IMAGEDEFAULT = "/shared/images/defaults/record.jpg";
export const DEFAULTAVATAR = "/shared/images/defaults/avatar.jpg";
export const EMPTYBOX = "/client/images/box-emtpy.png";

export const TOHOME = "/";
// Profile routes
export const PROFILEINDEX = "/profile";
export const PROFILECHANGEPASSWORD = "/profile/change-password";
export const PROFILEADDRESSORDER = "/profile/address_order";
export const PROFILEORDER = "/profile/order";
export const PROFILEADDRESSORDERCREATE = "/profile/address_order/create";
export const PROFILEADDRESSORDERUPDATE = (id: string | number) => `/profile/address_order/${id}/update`;

// Post route constants
// export const TOFORBIDDEN = "/forbidden";
// export const TO404 = "/404";
// export const TO500 = "/500";

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
