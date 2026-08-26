<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'date_of_create',
        'name',
        'price',
        'vat',
        'quantity',
        'free_items',
        'stock_quantity',
        'apply_company_discount',
    ];

    protected $casts = [
        'apply_company_discount' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** Discount % applied to this line (0 when product opts out of company discount). */
    public function lineDiscountPercent(?float $companyDiscountPercent = null): float
    {
        if (! ($this->apply_company_discount ?? true)) {
            return 0.0;
        }

        if ($companyDiscountPercent !== null) {
            return (float) $companyDiscountPercent;
        }

        $this->loadMissing('company');

        return (float) ($this->company->discount ?? 0);
    }

    public function linePriceAfterDiscount(?float $companyDiscountPercent = null): float
    {
        $gross = (float) $this->price * (int) $this->quantity;
        $pct = $this->lineDiscountPercent($companyDiscountPercent);

        return $gross - ($gross * $pct / 100);
    }

    public function lineVatAfterDiscount(?float $companyDiscountPercent = null): float
    {
        return $this->linePriceAfterDiscount($companyDiscountPercent) * ((float) $this->vat / 100);
    }

    public function getPriceWithVat()
    {

          $price_with_quntity =  $this->price*$this->quantity;
        // Calculate the price with VAT
        $priceWithVAT = $price_with_quntity + ($price_with_quntity * ($this->vat / 100));
        return $priceWithVAT;
    }
    public function getVatAmount()
{
    // Calculate the VAT amount only
    $priceWithQuantity = $this->price * $this->quantity;
    $vatAmount = $priceWithQuantity * ($this->vat / 100);
    return $vatAmount;
}


public function getVatDiscountAmount()
{
    // Calculate the VAT amount only
    $priceWithQuantity = $this->price * $this->quantity;
    $vatAmount = $priceWithQuantity * ($this->vat / 100);
    return $vatAmount;
}
    public function getPriceWithDiscount()
    {

          $price_with_quntity =  $this->price*$this->quantity;
        // Calculate the price with VAT
        $priceWithVAT = $price_with_quntity - ($price_with_quntity * ($this->discount / 100));
        return $priceWithVAT;
    }

    public function getPriceWithVatAndDiscount()
    {
        $price_with_quntity =  $this->price* $this->quantity;
        // Calculate the price with VAT
        $priceWithVAT = $price_with_quntity + ($price_with_quntity * ($this->vat / 100));

        // Calculate the discount amount
        $discountAmount = $priceWithVAT * ($this->lineDiscountPercent() / 100);

        // Calculate the final price after discount
        $finalPrice = $priceWithVAT - $discountAmount;

        return $finalPrice;

    }

    public function getTotalPriceWithVat()
{
    // Calculate the total price with VAT
    $price_with_quntity =  $this->price* $this->quantity;
    $totalPriceWithVAT = $price_with_quntity + ($price_with_quntity * $this->vat / 100);
    return $totalPriceWithVAT;
}

public function getTotalPriceWithVatAndDiscount()
{
    $price_with_quntity =  $this->price* $this->quantity;

    // Calculate the total price with VAT
    $totalPriceWithVAT = $price_with_quntity + ($price_with_quntity * $this->vat / 100);

    // Apply the company discount
    $discountAmount = $totalPriceWithVAT * ($this->lineDiscountPercent() / 100);
    $totalPriceWithVatAndDiscount = $totalPriceWithVAT - $discountAmount;

    return $totalPriceWithVatAndDiscount;
}




}