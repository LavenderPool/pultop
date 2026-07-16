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
};
