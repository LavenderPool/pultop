export type Admin = {
    id: number;
    name: string;
    email: string;
};

export type SharedData = {
    name: string;
    auth: {
        admin: Admin | null;
    };
    flash?: {
        success?: string | null;
    };
};

export type Bank = {
    id: number;
    name: string;
    slug: string;
    address: string | null;
    description: string | null;
    website: string | null;
    license: string | null;
    mfo: string | null;
    inn: string | null;
    parser_code: string | null;
    rates_url: string | null;
    is_active: boolean;
    sort_order: number;
    logo_url: string | null;
};

