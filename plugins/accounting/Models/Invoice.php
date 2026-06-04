<?php

namespace Plugins\accounting\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'client_name',
        'client_email',
        'issue_date',
        'due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date'   => 'date',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getTotalAttribute()
    {
        return $this->items->sum(fn($item) => $item->qty * $item->price);
    }
}