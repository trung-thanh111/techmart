export interface ISystem {
  id?: number;
  keyword?: string;
  content?: string | null;
  publish?: number;
  userId?: number | null;
  createdAt?: string;
  updatedAt?: string;
}

export interface SystemPayloadFilter {
  keyword?: string | null;
  publish?: number | null;
  perpage?: number;
  page?: number;
}

export interface ISystemGeneralConfig {
  company_name?: string;
  short_name?: string;
  slogan?: string;
  logo?: string;
  favicon?: string;
  copyright?: string;
  tax_code?: string;
  working_hours?: string;
  intro?: string;
}

export interface ISystemContactConfig {
  hotline?: string;
  hotline_secondary?: string;
  complaints_hotline?: string;
  email?: string;
  email_complaint?: string;
  address?: string;
  map_iframe?: string;
}

export interface ISystemSeoConfig {
  meta_title?: string;
  meta_keywords?: string;
  meta_description?: string;
  og_image?: string;
  robots?: string;
}

export interface ISystemSocialConfig {
  facebook?: string;
  youtube?: string;
  tiktok?: string;
  zalo?: string;
  messenger?: string;
  instagram?: string;
}

export interface ISystemScriptConfig {
  head_script?: string;
  body_script?: string;
}

export interface ISystemPublicConfigs {
  general: ISystemGeneralConfig;
  contact: ISystemContactConfig;
  seo: ISystemSeoConfig;
  social: ISystemSocialConfig;
  script: ISystemScriptConfig;
  map?: Record<string, string>;
}

export interface ISystemFieldSchema {
  label: string;
  type: "text" | "textarea" | "image" | "email" | "url";
  placeholder?: string;
  default?: string;
  keyword: string;
  value?: string;
}

export interface ISystemGroupSchema {
  label: string;
  description?: string;
  fields: Record<string, ISystemFieldSchema>;
}
