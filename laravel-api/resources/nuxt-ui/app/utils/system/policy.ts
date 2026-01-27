export type PolicyKeyword = "privacy_policy" | "warranty_policy" | "delivery_policy" | "return_policy";

export interface SidebarItem {
    title: string;
    path: string;
    keyword?: string;
}

export interface MenuSection {
    title: string;
    items: SidebarItem[];
}

export const POLICY_KEYWORDS: Record<PolicyKeyword, string> = {
    privacy_policy: "Bảo mật",
    warranty_policy: "Bảo hành",
    delivery_policy: "Vận chuyển & giao hàng",
    return_policy: "Đổi trả hàng",
};

export const SUPPORT_MENU: SidebarItem[] = [
    {
        title: "Liên hệ hỗ trợ",
        path: "/contact",
    },
    {
        title: "Câu hỏi thường gặp",
        path: "/faq",
    },
];

export const GUIDE_MENU: SidebarItem[] = [
    {
        title: "Cách đặt hàng",
        path: "/guide/order",
    },
    {
        title: "Theo dõi đơn hàng",
        path: "/guide/maneger-order",
    },
    {
        title: "Quản lý tài khoản",
        path: "/guide/maneger-account",
    },
];

export const getPolicyTitle = (keyword: string): string => {
    return POLICY_KEYWORDS[keyword as PolicyKeyword] || keyword;
};

export const getPolicySidebarItems = (policies: any[]): SidebarItem[] => {
    return policies
        .filter((policy) => policy.keyword && policy.keyword in POLICY_KEYWORDS)
        .map((policy) => ({
            title: getPolicyTitle(policy.keyword),
            path: `/policy/${policy.keyword}`,
            keyword: policy.keyword,
        }));
};

export const getMenuSections = (policies: any[]): MenuSection[] => {
    return [
        {
            title: "Chính sách",
            items: getPolicySidebarItems(policies),
        },
        {
            title: "Hỗ trợ",
            items: SUPPORT_MENU,
        },
        {
            title: "Hướng dẫn",
            items: GUIDE_MENU,
        },
    ];
};
