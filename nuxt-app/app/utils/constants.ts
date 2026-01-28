export const MAX_FILE_SIZE = 5 * 1024 * 1024;
export const ACCEPTED_IMAGE_TYPES = ["image/jpeg", "image/png", "image/webp", "image/jpg"];

export const IMAGEDEFAULT = "/images/defaults/record.jpg";
export const DEFAULTAVATAR = "/images/defaults/avatar.jpg";
export const EMPTYBOX = "/images/box-emtpy.png";

export const TOHOME = "/";
export const TOVOUCHER = "/voucher";
export const TOSHOP = "/shop";
export const TOWISHLIST = "/wishlist";
export const TOAGENCY = "/agency";
export const TOAGENCYSYSTEM = "/agency";
export const TOFAQ = "/faq";
export const TOPOST = "/post";
export const TOCART = "/cart";
export const TOCOMPARE = "/product/compare";
export const TOSEARCH = "/search";
export const PRODUCT = "/product";
export const PRODUCTCATEGORY = "/product/category";
export const POSTCATEGORY = "/post/category";
export const PRODUCTCATEGORIES = "/product/categories";
export const POSTCATEGORIES = "/post/categories";

export const TOPOSTDETAIL = (slug: string) => `/post/${slug}`;
export const TOPOSTCATEGORY = "/post/category";
export const PROFILEINDEX = "/profile";
export const PROFILECHANGEPASSWORD = "/profile/change-password";
export const PROFILEADDRESSORDER = "/profile/address_order";
export const PROFILEORDERS = "/profile/orders";
export const PROFILEADDRESSORDERUPDATE = (id: string | number) => `/profile/address_order/${id}/update`;
export const PROFILEADDRESSORDERCREATE = "/profile/address_order/create";
export const PROFILEADDRESSORDERINDEX = "/profile/address_order";
export const PROFILEADRESSORDERCREATE = "/profile/address_order/create";
export const PROFILEADRESSORDERUPDATE = "/profile/address_order/update";

export const TOLOGIN = "/auth/login";
export const TOREGISTER = "/auth/register";
export const TOFORGOT = "/forgot-password";
export const TOFORGOTOTP = "/forgot-password/otp";
export const TOFORGOTRESET = "/forgot-password/reset";

export const TOPOLICY = "/policy/privacy_policy";
export const TOPOLICIES = "/policy";
export const TOEXCHANGEPOLICY = "/policy/return_policy";
export const TOWARRANTYPOLICY = "/policy/warranty_policy";
export const TOLIVERYPOLICY = "/policy/delivery_policy";
export const TOPRIVACYPOLICY = "/policy/privacy_policy";
export const HOTLINE = "tel:19002098";
export const SUPPORT_BASE = "/support";
export const SUPPORT_CONTACT = "/support/contact";
export const SUPPORT_FAQS = "/support/faqs";
export const GUIDE_BASE = "/guide";
export const GUIDE_ORDER = "/guide/order";
export const GUIDE_MANAGER_ORDER = "/guide/maneger-order";
export const GUIDE_MANAGER_ACCOUNT = "/guide/maneger-account";

export const PRODUCTCATALOGUEINDEX = "/product/catalogue";
export const POSTTAGINDEX = "/post/tag";
export const ATTRIBUTEINDEX = "/attribute";

export const TOFORBIDDEN = "/forbidden";
export const TO404 = "/404";
export const TO500 = "/500";

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
