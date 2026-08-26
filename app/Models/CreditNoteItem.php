<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_note_id',
        'product_id',
        'item_name',
        'quantity',
        'free_items',
        'unit_price',
        'vat_percent',
        'company_discount_percent',
        'line_note',
    ];

    public function creditNote(): BelongsTo
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Line total before VAT, after company discount (positive magnitude). */
    public function priceAfterDiscount(): float
    {
        $gross = $this->unit_price * $this->quantity;

        return (float) ($gross - $gross * ($this->company_discount_percent / 100));
    }

    /** VAT amount on discounted line (positive magnitude). */
    public function vatAmount(): float
    {
        return (float) ($this->priceAfterDiscount() * ($this->vat_percent / 100));
    }

    /** Line total incl. VAT (positive magnitude). */
    public function lineTotal(): float
    {
        return $this->priceAfterDiscount() + $this->vatAmount();
    }
}
