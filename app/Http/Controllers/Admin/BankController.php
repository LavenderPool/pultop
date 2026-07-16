<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Bank\StoreBankRequest;
use App\Http\Requests\Admin\Bank\UpdateBankRequest;
use App\Models\Bank;
use App\Services\BankService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BankController extends Controller
{
    public function __construct(
        private readonly BankService $banks,
    ) {}

    public function index(): Response
    {
        $items = Bank::query()
            ->ordered()
            ->get()
            ->map(fn (Bank $bank) => $this->transform($bank));

        return Inertia::render('admin/banks/index', [
            'banks' => $items,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/banks/create', [
            'parserCodes' => [],
        ]);
    }

    public function store(StoreBankRequest $request): RedirectResponse
    {
        $this->banks->create(
            $request->safe()->except('logo'),
            $request->file('logo'),
        );

        return redirect()
            ->route('admin.banks.index')
            ->with('success', 'Банк создан.');
    }

    public function edit(Bank $bank): Response
    {
        return Inertia::render('admin/banks/edit', [
            'bank' => $this->transform($bank),
            'parserCodes' => [],
        ]);
    }

    public function update(UpdateBankRequest $request, Bank $bank): RedirectResponse
    {
        $this->banks->update(
            $bank,
            $request->safe()->except('logo'),
            $request->file('logo'),
        );

        return redirect()
            ->route('admin.banks.index')
            ->with('success', 'Банк обновлён.');
    }

    public function destroy(Bank $bank): RedirectResponse
    {
        $this->banks->delete($bank);

        return redirect()
            ->route('admin.banks.index')
            ->with('success', 'Банк удалён.');
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Bank $bank): array
    {
        return [
            'id' => $bank->id,
            'name' => $bank->name,
            'slug' => $bank->slug,
            'website' => $bank->website,
            'parser_code' => $bank->parser_code,
            'rates_url' => $bank->rates_url,
            'is_active' => $bank->is_active,
            'sort_order' => $bank->sort_order,
            'logo_url' => $bank->logoUrl(),
        ];
    }
}
