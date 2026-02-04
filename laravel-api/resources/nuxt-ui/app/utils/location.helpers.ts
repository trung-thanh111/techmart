import addressData from "~/data/address_vietnam.json";

interface AddressUnit {
    name: string;
    code: number;
    codename: string;
    division_type: string;
    short_codename?: string;
    phone_code?: number;
    wards?: AddressUnit[];
}

const addresses = addressData as AddressUnit[];

export const getCityName = (codename?: string): string => {
    if (!codename) return "";
    const city = addresses.find((c) => c.codename === codename);
    return city ? city.name : codename;
};

export const getWardName = (cityCodename?: string, wardCodename?: string): string => {
    if (!wardCodename) return "";
    if (cityCodename) {
        const city = addresses.find((c) => c.codename === cityCodename);
        if (city && city.wards) {
            const ward = city.wards.find((d) => d.codename === wardCodename);
            return ward ? ward.name : wardCodename;
        }
    }
    return wardCodename;
};

export const getCityOptions = () => {
    return addresses.map((c) => ({ label: c.name, value: c.codename }));
};

export const getWardOptions = (cityCodename?: string) => {
    if (!cityCodename) return [];
    const city = addresses.find((c) => c.codename === cityCodename);
    return city?.wards?.map((w) => ({ label: w.name, value: w.codename })) || [];
};
