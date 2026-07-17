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

export type ArticleCategoryOption = {
    value: string;
    label: string;
};

export type Article = {
    id: number;
    title: string;
    slug: string;
    category: string;
    category_label: string;
    excerpt: string | null;
    body: string;
    meta_title: string | null;
    meta_description: string | null;
    published_at: string | null;
    is_published: boolean;
    cover_url: string | null;
};

export type BankOption = {
    id: number;
    name: string;
};

export type CreditTypeOption = {
    id: number;
    slug: string;
    name: string;
};

export type CreditRateRow = {
    rate: string;
    term?: string | null;
    note?: string | null;
};

export type CreditConditionRow = {
    label: string;
    value?: string | null;
};

export type Credit = {
    id: number;
    title: string;
    slug: string;
    bank_id: number | null;
    bank_name?: string | null;
    currency: string;
    rate_display: string | null;
    term_display: string | null;
    amount_display: string | null;
    down_payment: string | null;
    special_conditions?: string | null;
    apply_url: string | null;
    is_active: boolean;
    sort_order: number;
    type_ids: number[];
    type_names: string[];
    rate_rows?: CreditRateRow[];
    conditions?: CreditConditionRow[];
};

export type DepositRateRow = {
    rate: string;
    term?: string | null;
    note?: string | null;
};

export type DepositConditionRow = {
    label: string;
    value?: string | null;
};

export type Deposit = {
    id: number;
    title: string;
    slug: string;
    bank_id: number | null;
    bank_name?: string | null;
    currency: string;
    rate_display: string | null;
    term_display: string | null;
    amount_display: string | null;
    term_min_months: number | null;
    term_max_months: number | null;
    amount_min: number | null;
    amount_max: number | null;
    early_termination: boolean;
    partial_withdrawal: boolean;
    capitalization: boolean;
    is_online: boolean;
    special_conditions?: string | null;
    apply_url: string | null;
    is_active: boolean;
    sort_order: number;
    rate_rows?: DepositRateRow[];
    conditions?: DepositConditionRow[];
};

export type CardTypeOption = {
    value: string;
    label: string;
};

export type CardConditionRow = {
    label: string;
    value?: string | null;
    note?: string | null;
};

export type BankCard = {
    id: number;
    title: string;
    slug: string;
    bank_id: number | null;
    bank_name?: string | null;
    currency: string;
    payment_system: string | null;
    card_type: string | null;
    card_type_label?: string | null;
    category: string | null;
    issue_cost_display: string | null;
    validity_display: string | null;
    special_conditions?: string | null;
    apply_url: string | null;
    image_url: string | null;
    is_active: boolean;
    sort_order: number;
    conditions?: CardConditionRow[];
};

