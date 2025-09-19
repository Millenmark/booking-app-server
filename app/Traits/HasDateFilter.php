<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Carbon\Carbon;

trait HasDateFilter
{
  /**
   * Scope to apply date filter based on request.
   */
  public function scopeWithDateFilter($query, $request)
  {
    if ($request->filled('date_from') && $request->filled('date_to')) {
      $query->whereBetween('created_at', [
        Carbon::parse($request->date_from)->startOfDay(),
        Carbon::parse($request->date_to)->endOfDay(),
      ]);
    } else {
      $query->whereDate('created_at', Carbon::today());
    }

    return $query;
  }
}
