<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Requests\CreditNoteRequest;
use App\Services\CreditNoteService;
use Illuminate\Support\Facades\Log;

class CreditNoteController extends Controller
{
    public function __construct(
        protected CreditNoteService $creditNoteService,
    ) {
    }

    public function index()
    {
        return view('credit-note.index');
    }

    public function create()
    {
        $invoices = Invoice::with('company')->orderByDesc('id')->get();

        return view('credit-note.create', compact('invoices'));
    }

    public function preview(Invoice $invoice)
    {
        $payload = $this->creditNoteService->buildInvoicePreview($invoice);

        return response()->json($payload);
    }

    public function store(CreditNoteRequest $request)
    {
        $this->creditNoteService->store($request);

        return redirect()->route('credit-note.index')->with('success', 'Credit note created successfully.');
    }

    public function show(int $id)
    {
        $creditNote = $this->creditNoteService->find($id);

        return view('credit-note.show', compact('creditNote'));
    }

    public function edit(int $id)
    {
        $creditNote = $this->creditNoteService->find($id);

        return view('credit-note.edit', compact('creditNote'));
    }

    public function update(CreditNoteRequest $request, int $id)
    {
        $this->creditNoteService->update($id, $request);

        return redirect()->route('credit-note.index')->with('success', 'Credit note updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->creditNoteService->delete($id);

        return redirect()->back()->with('success', 'Credit note deleted successfully.');
    }

    public function downloadCreditNote(int $id)
    {
        $creditNote = $this->creditNoteService->find($id);

        $html = view('credit-note.download', compact('creditNote'))->render();

        try {
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output('credit_note_' . $creditNote->id . '.pdf', 'D');
        } catch (\Mpdf\MpdfException $e) {
            Log::error('mPDF Credit Note Error: ' . $e->getMessage());

            return response()->json(['error' => 'PDF generation failed.'], 500);
        }
    }
}
