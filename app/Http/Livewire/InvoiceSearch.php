<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceSearch extends Component
{
    use WithPagination;

    public $search = '';

    public $companyId = '';

    public $month = '';

    public $year = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'companyId' => ['except' => ''],
        'month' => ['except' => ''],
        'year' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCompanyId(): void
    {
        $this->resetPage();
    }

    public function updatingMonth(): void
    {
        $this->resetPage();
    }

    public function updatingYear(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->companyId = '';
        $this->month = '';
        $this->year = '';
        $this->resetPage();
    }

    public function paginationView(): string
    {
        return 'livewire.pagination';
    }

    public function render()
    {
        $term = '%' . $this->search . '%';
        $companyId = $this->companyId !== '' ? (int) $this->companyId : null;
        $month = $this->month !== '' ? (int) $this->month : null;
        $year = $this->year !== '' ? (int) $this->year : null;

        $invoices = Invoice::query()
            ->with('company')
            ->forCompany($companyId)
            ->forMonthYear($month, $year)
            ->when($this->search !== '', function ($query) use ($term) {
                $query->where(function ($q) use ($term) {
                    $q->where('invoice_number', 'like', $term)
                        ->orWhere('status', 'like', $term)
                        ->orWhereDate('date_of_create', 'like', $term)
                        ->orWhereHas('company', function ($companyQuery) use ($term) {
                            $companyQuery->where('name', 'like', $term);
                        });
                });
            })
            ->orderByDesc('date_of_create')
            ->paginate(10);

        $years = Invoice::query()
            ->selectRaw('YEAR(`from`) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        if ($years->isEmpty()) {
            $years = collect([(int) date('Y')]);
        }

        return view('livewire.invoice-search', [
            'invoices' => $invoices,
            'companies' => Company::orderBy('name')->get(),
            'years' => $years,
        ]);
    }
}
