<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

trait CommonQueryScopes
{
    /**
     * Filter records by date (events happening on or after specified date)
     */
    public function scopeFilterByDate(Builder $query, $date = null)
    {
        if ($date) {
            try {
                $carbonDate = Carbon::parse($date);
                return $query->whereDate('date', '>=', $carbonDate->format('Y-m-d'));
            } catch (\Exception $e) {
                // If date parsing fails, return query unchanged
                return $query;
            }
        }
        return $query;
    }

    /**
     * Search records by title (case-insensitive partial match)
     */
    public function scopeSearchByTitle(Builder $query, $search = null)
    {
        if ($search && trim($search) !== '') {
            $searchTerm = '%' . strtolower(trim($search)) . '%';
            return $query->whereRaw('LOWER(title) LIKE ?', [$searchTerm]);
        }
        return $query;
    }

    /**
     * Filter by date range
     */
    public function scopeFilterByDateRange(Builder $query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->whereDate('date', '>=', Carbon::parse($startDate));
        }

        if ($endDate) {
            $query->whereDate('date', '<=', Carbon::parse($endDate));
        }

        return $query;
    }

    /**
     * Get upcoming records (future dates only)
     */
    public function scopeUpcoming(Builder $query)
    {
        return $query->whereDate('date', '>=', Carbon::today());
    }

    /**
     * Get past records (past dates only)
     */
    public function scopePast(Builder $query)
    {
        return $query->whereDate('date', '<', Carbon::today());
    }

    /**
     * Order by date (ascending by default)
     */
    public function scopeOrderByDate(Builder $query, $direction = 'asc')
    {
        return $query->orderBy('date', $direction);
    }
}
