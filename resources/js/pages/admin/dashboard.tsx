import { Head, usePage } from '@inertiajs/react';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { SharedData } from '@/types';

export default function Dashboard() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Dashboard" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Dashboard
                    </h1>
                    <p className="text-muted-foreground">
                        Welcome back{auth.admin ? `, ${auth.admin.name}` : ''}.
                    </p>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Signed in</CardTitle>
                            <CardDescription>
                                You are authenticated as an admin.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-1 text-sm">
                            <p>
                                <span className="text-muted-foreground">
                                    Name:{' '}
                                </span>
                                {auth.admin?.name}
                            </p>
                            <p>
                                <span className="text-muted-foreground">
                                    Email:{' '}
                                </span>
                                {auth.admin?.email}
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Next steps</CardTitle>
                            <CardDescription>
                                Build admin modules on top of this foundation.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="text-sm text-muted-foreground">
                            Auth, Inertia + React, Wayfinder, and shadcn/ui are
                            ready. Add resources and CRUD from here.
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
