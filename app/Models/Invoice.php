<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'from','to', 'date_of_create' , 'invoice_number', 'status'];

    public function scopeForCompany(Builder $query, ?int $companyId): Builder
    {
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query;
    }

    public function scopeForMonthYear(Builder $query, ?int $month, ?int $year): Builder
    {
        if (! $year && ! $month) {
            return $query;
        }

        if ($year && $month) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            return $query->where('from', '<=', $end)->where('to', '>=', $start);
        }

        if ($year) {
            return $query->whereYear('from', $year);
        }

        return $query->whereMonth('from', $month);
    }

    public function company() :BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }

    public function calculateTotalWithVat()
    {
        $totalWithVat = 0;

        // Assuming you have a relationship to get products linked to the invoice
        // Fetch products belonging to the company of this invoice
        $products = Product::where('company_id', $this->company_id)->get();

        foreach ($products as $product) {
            // Calculate total with VAT for each product
            $totalWithVat += $product->getTotalPriceWithVat();
        }

        return $totalWithVat;
    }
}