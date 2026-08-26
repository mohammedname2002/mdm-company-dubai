<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use Dompdf\Dompdf;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Product;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;

use App\Services\CompanyService;
use App\Services\InvoiceService;
use App\Http\Requests\InvoiceRequest;
use Barryvdh\Snappy\Facades\SnappyPdf;
use App\Http\Requests\UpdateInvoiceRequest;
use Log;

class InvoiceController extends Controller
{
    protected $invoiceService;
    protected $companyService;

    public function __construct(InvoiceService $invoiceService, CompanyService $companyService)
    {
        $this->invoiceService = $invoiceService;
        $this->companyService = $companyService;
    }

    public function index()
    {
        $paginate = request()->paginate ?? 10;
        $invoices = $this->invoiceService->index([], [], ['*'], $paginate);
        $companies = Company::orderBy('name')->get();

        $years = Invoice::query()
            ->selectRaw('YEAR(`from`) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        if ($years->isEmpty()) {
            $years = collect([(int) date('Y')]);
        }

        return view('invoice.index', [
            'invoices' => $invoices,
            'companies' => $companies,
            'years' => $years,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = $this->invoiceService->create();

        return view(
            'invoice.create',
            ['companies' => $companies]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        $this->invoiceService->store($request);
        return redirect()->route('invoice.index')->with('success', 'Added Sccesfully ');
    }


    public function edit($id)
    {
        $invoice = $this->invoiceService->find($id, ['*']);
        $companies = Company::all();

        return view('invoice.edit', [
            'invoice' => $invoice,
            'companies' => $companies
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update($id, InvoiceRequest $request)
    {

        $this->invoiceService->update($id, $request);

        return redirect()->route('invoice.index')->with('success', 'Edited Sccesfully ');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = $this->invoiceService->delete($id);
        return redirect()->back()->with('success', 'Deleted Successfully');
    }

    public function show($id)

            {


            $products  = $this->invoiceService->show($id);
            $invoiceName = Invoice::where('id' , $id)->with(['company'])->first();
        // Fetch all products that belong to this invoice's company
        // $products  = Product::where('company_id', $invoiceName->company->id)->get();

        $totalWithVatAndDiscount = $products->sum(function ($product) {
            return $product->getPriceWithVatAndDiscount();
        });
        $totalWithVat = $products->sum(function ($product) {
            return $product->getPriceWithVat(); // Assuming this method is defined to calculate price with VAT
        });


        $totalTotal = $products->sum(function ($product) use ($invoiceName) {
            $companyDisc = (float) ($invoiceName->company->discount ?? 0);
            $productPriceWithDiscont = $product->linePriceAfterDiscount($companyDisc);

            return $productPriceWithDiscont + ($productPriceWithDiscont * $product->vat / 100);
        });

return  view('invoice.show', [
            'products'=>$products
          , 'invoiceName'=>$invoiceName
         ,'totalWithVat'=>$totalWithVat
            ,'totalWithVatAndDiscount'=>$totalWithVatAndDiscount
            ,'totalTotal'=>$totalTotal
            ,'multiPagePrint' => request()->boolean('multi_print'),
      ]);
    }


    public function printByCompany(Request $request, $companyId)
    {
        $month = $request->filled('month') ? (int) $request->month : null;
        $year = $request->filled('year') ? (int) $request->year : null;

        $companyIdInt = ($companyId === 'all') ? null : (int) $companyId;

        $data = $this->invoiceService->getCompanyInvoicesPrintData($companyIdInt, $month, $year);

        if (empty($data['invoices'])) {
            return redirect()
                ->route('invoice.index')
                ->with('error', 'No invoices found for the selected filters.');
        }

        return view('invoice.print-by-company', $data);
    }

    public function downloadInvoice($id)
    {
        \Log::info("Download Invoice Process Started");

        $products = $this->invoiceService->show($id);
        $invoiceName = Invoice::where('id', $id)->with(['company'])->first();

        $totalWithVatAndDiscount = $products->sum(function ($product) {
            return $product->getPriceWithVatAndDiscount();
        });
        $totalWithVat = $products->sum(function ($product) {
            return $product->getPriceWithVat(); // Assuming this method is defined to calculate price with VAT
        });

        \Log::info("Data fetched: ", [
            'products' => $products,
            'invoiceName' => $invoiceName,
            'totalWithVat' => $totalWithVat,
            'totalWithVatAndDiscount' => $totalWithVatAndDiscount
        ]);

        $html = view('invoice.download', [
            'products' => $products,
            'invoiceName' => $invoiceName,
            'totalWithVat' => $totalWithVat,
            'totalWithVatAndDiscount' => $totalWithVatAndDiscount
        ])->render();

        // Check if HTML is generated correctly
        \Log::info("HTML content generated");

        // Initialize mPDF
        try {
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);

            // Force the PDF download
            $mpdf->Output('invoice_' . $invoiceName->id . '.pdf', 'D'); // 'D' forces download

            \Log::info("PDF generated and download triggered.");
        } catch (\Mpdf\MpdfException $e) {
            \Log::error('mPDF Error: ' . $e->getMessage());
            return response()->json(['error' => 'PDF generation failed!'], 500);
        }
    }

}