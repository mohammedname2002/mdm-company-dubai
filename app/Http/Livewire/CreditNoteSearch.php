<?php

namespace App\Http\Livewire;

use App\Models\CreditNote;
use Livewire\Component;
use Livewire\WithPagination;

class CreditNoteSearch extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function paginationView(): string
    {
        return 'livewire.pagination';
    }

    public function render()
    {
        $term = '%' . $this->search . '%';

        $creditNotes = CreditNote::query()
            ->with(['invoice.company'])
            ->when($this->search !== '', function ($q) use ($term) {
                $q->where(function ($q2) use ($term) {
                    $q2->where('credit_note_number', 'like', $term)
                        ->orWhere('reason', 'like', $term)
                        ->orWhereHas('invoice', function ($iq) use ($term) {
                            $iq->where('invoice_number', 'like', $term);
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.credit-note-search', [
            'creditNotes' => $creditNotes,
        ]);
    }
}
