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
    protected $fillable = ['company_id', 'date_of_create', 'name', 'price', 'vat', 'quantity', 'free_items'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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
        $discountAmount = $priceWithVAT * ($this->company->discount / 100);

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
    $discountAmount = $totalPriceWithVAT * ($this->company->discount / 100);
    $totalPriceWithVatAndDiscount = $totalPriceWithVAT - $discountAmount;

    return $totalPriceWithVatAndDiscount;
}




}