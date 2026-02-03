import vietnamData from "../../data/address_vietnam.json";

interface City {
  name: string;
  codename: string;
  wards: { name: string; codename: string }[];
}

export const getCityName = (codename: string): string => {
  if (!codename) return "";
  const city = (vietnamData as City[]).find((c) => c.codename === codename);
  return city?.name || codename;
};

export const getWardName = (cityCodename: string, wardCodename: string): string => {
  if (!cityCodename || !wardCodename) return "";
  const city = (vietnamData as City[]).find((c) => c.codename === cityCodename);
  if (!city) return wardCodename;
  const ward = city.wards.find((w) => w.codename === wardCodename);
  return ward?.name || wardCodename;
};

export const formatShippingAddress = (order: { shippingStreet?: string; shippingWard?: string; shippingCity?: string; shippingAddress?: string }): string => {
  const parts: string[] = [];

  if (order.shippingStreet) parts.push(order.shippingStreet);

  const wardName = getWardName(order.shippingCity || "", order.shippingWard || "");
  if (wardName) parts.push(wardName);

  const cityName = getCityName(order.shippingCity || "");
  if (cityName) parts.push(cityName);

  if (parts.length === 0 && order.shippingAddress) {
    return order.shippingAddress;
  }

  return parts.join(", ");
};
