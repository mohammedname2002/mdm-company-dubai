<div>
    <input type="text" wire:model="search" placeholder="Search credit notes..." class="form-control mb-3" />

    <div class="table-responsive">
        <table class="table table-centered table-nowrap mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Credit Note No.</th>
                    <th>Date</th>
                    <th>Original Invoice</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($creditNotes as $cn)
                    <tr>
                        <td>{{ $creditNotes->firstItem() + $loop->index }}</td>
                        <td>{{ $cn->credit_note_number }}</td>
                        <td>{{ $cn->note_date->format('Y-m-d') }}</td>
                        <td>{{ $cn->invoice->invoice_number ?? '—' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($cn->reason, 40) }}</td>
                        <td style="display: inline-block">
                            <div class="button-list" style="display: flex; flex-wrap: wrap; gap: 4px;">
                                <form action="{{ route('credit-note.show', $cn->id) }}" method="get">
                                    <button type="submit" class="btn btn-success waves-effect waves-light btn-sm">View</button>
                                </form>
                                <form action="{{ route('credit-note.edit', $cn->id) }}" method="get">
                                    <button type="submit" class="btn btn-secondary waves-effect waves-light btn-sm">Edit</button>
                                </form>
                                <form action="{{ route('credit-note.delete', $cn->id) }}" id="deletecreditnote{{ $cn->id }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="JSconfirmCreditNote(event, {{ $cn->id }})" class="btn btn-danger waves-effect waves-light btn-sm">Delete</button>
                                </form>
                                <form action="{{ route('credit-note.download', $cn->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary waves-effect waves-light btn-sm">PDF</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No credit notes found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $creditNotes->links() }}
    </div>
</div>
