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
    website: string | null;
    parser_code: string | null;
    rates_url: string | null;
    is_active: boolean;
    sort_order: number;
    logo_url: string | null;
};

