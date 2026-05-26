<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['amount', 'category_id', 'month', 'user_id'])]
class Budget extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'amount' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeGlobalForMonth($query, $year, $month)
    {
        return $query->whereNull('category_id')
            ->whereYear('month', $year)
            ->whereMonth('month', $month);
    }

    public function scopeCategoryForMonth($query, $categoryId, $year, $month)
    {
        return $query->where('category_id', $categoryId)
            ->whereYear('month', $year)
            ->whereMonth('month', $month);
    }
}
