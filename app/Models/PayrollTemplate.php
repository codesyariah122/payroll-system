<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollTemplate extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'type',
        'html_content',
        'pdf_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function isHtml(): bool
    {
        return $this->type === 'html';
    }

    public function isPdfReference(): bool
    {
        return $this->type === 'pdf';
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
