<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EventCacheService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_PREFIX = 'events:';

    /**
     * Get cached events list with pagination
     */
    public function getCachedEvents(array $params = []): mixed
    {
        $cacheKey = $this->generateCacheKey('list', $params);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($params) {
            Log::info('Caching events list', ['params' => $params]);

            return $this->getEventsFromDatabase($params);
        });
    }

    /**
     * Get single cached event
     */
    public function getCachedEvent(int $eventId): mixed
    {
        $cacheKey = $this->generateCacheKey('single', ['id' => $eventId]);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($eventId) {
            Log::info('Caching single event', ['event_id' => $eventId]);

            return Event::with(['creator', 'tickets.bookings'])->find($eventId);
        });
    }

    /**
     * Get popular events (most bookings)
     */
    public function getPopularEvents(int $limit = 10): mixed
    {
        $cacheKey = $this->generateCacheKey('popular', ['limit' => $limit]);

        return Cache::remember($cacheKey, self::CACHE_TTL * 2, function () use ($limit) {
            Log::info('Caching popular events', ['limit' => $limit]);

            return Event::with(['creator', 'tickets'])
                ->withCount(['bookings' => function ($query) {
                    $query->where('status', 'confirmed');
                }])
                ->orderBy('bookings_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get upcoming events cache
     */
    public function getUpcomingEvents(int $limit = 20): mixed
    {
        $cacheKey = $this->generateCacheKey('upcoming', ['limit' => $limit]);

        return Cache::remember($cacheKey, self::CACHE_TTL / 2, function () use ($limit) {
            Log::info('Caching upcoming events', ['limit' => $limit]);

            return Event::with(['creator', 'tickets'])
                ->upcoming()
                ->orderByDate()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Clear event cache
     */
    public function clearEventCache(?int $eventId = null): void
    {
        if ($eventId) {
            // Clear specific event cache
            $patterns = [
                self::CACHE_PREFIX . "single:id:{$eventId}",
                self::CACHE_PREFIX . "list:*",
                self::CACHE_PREFIX . "popular:*",
                self::CACHE_PREFIX . "upcoming:*",
            ];
        } else {
            // Clear all event caches
            $patterns = [self::CACHE_PREFIX . '*'];
        }

        foreach ($patterns as $pattern) {
            $this->clearCacheByPattern($pattern);
        }

        Log::info('Event cache cleared', ['event_id' => $eventId]);
    }

    /**
     * Clear cache when events are modified
     */
    public function invalidateEventCache(int $eventId): void
    {
        $this->clearEventCache($eventId);
    }

    /**
     * Generate cache key
     */
    private function generateCacheKey(string $type, array $params = []): string
    {
        $paramString = !empty($params) ? ':' . http_build_query($params) : '';
        return self::CACHE_PREFIX . $type . $paramString;
    }

    /**
     * Get events from database with filters
     */
    private function getEventsFromDatabase(array $params = []): mixed
    {
        $perPage = $params['per_page'] ?? 10;
        $search = $params['search'] ?? null;
        $date = $params['date'] ?? null;
        $location = $params['location'] ?? null;

        return Event::with(['creator', 'tickets'])
            ->searchByTitle($search)
            ->filterByDate($date)
            ->when($location, function ($query, $location) {
                return $query->where('location', 'LIKE', '%' . $location . '%');
            })
            ->orderBy('date', 'asc')
            ->paginate($perPage);
    }

    /**
     * Clear cache by pattern (Redis specific)
     */
    private function clearCacheByPattern(string $pattern): void
    {
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            $keys = $redis->keys($pattern);
            if (!empty($keys)) {
                $redis->del($keys);
            }
        } else {
            // For non-Redis cache stores, we can't pattern match
            // This is a limitation when not using Redis
            Log::warning('Pattern cache clearing only works with Redis');
        }
    }
}
