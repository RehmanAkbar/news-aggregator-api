<?php

namespace App\Jobs;

use Exception;
use Carbon\Carbon;
use App\Services\ArticleService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SyncArticlesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public $backoff = [60, 300, 600]; // 1 minute, 5 minutes, 10 minutes

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected readonly bool $force = false
    ) {
        $this->onQueue('articles');
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'sync_articles';
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array 
    {
        return [60, 300, 600]; // 1 minute, 5 minutes, 10 minutes
    }

    /**
     * Execute the job.
     */
    public function handle(ArticleService $articleService): void
    {
        try {
            if (!$this->shouldSync()) {
                Log::info('Skipping article sync: minimum interval not reached');
                return;
            }

            Log::info('Starting article synchronization...', [
                'job_id' => $this->job->getJobId(),
                'attempt' => $this->attempts(),
                'force' => $this->force,
            ]);

            $startTime = now();

            // Fetch articles
            $stats = $articleService->fetchAndStoreArticles();

            // Update last sync time
            cache()->put('last_article_sync', $startTime);

            // Log success
            $this->logSuccess($stats);

        } catch (Exception $e) {
            $this->logError($e);
            
            if ($this->attempts() >= $this->tries) {
                $this->fail($e);
            }
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Exception $e): void
    {
        Log::error('Article sync job failed', [
            'job_id' => $this->job->getJobId(),
            'attempt' => $this->attempts(),
            'error' => $e ? $e->getMessage() : 'Unknown error',
            'trace' => $e ? $e->getTraceAsString() : '',
        ]);
    }

    /**
     * Determine if articles should be synced based on the last sync time
     * and minimum interval, unless forced.
     */
    protected function shouldSync(): bool
    {
        if ($this->force) {
            return true;
        }

        $lastSync = cache()->get('last_article_sync');
        
        if (!$lastSync) {
            return true;
        }

        $lastSyncTime = Carbon::parse($lastSync);
        $minutesSinceLastSync = $lastSyncTime->diffInMinutes(now());
        
        return $minutesSinceLastSync >= config('news-sources.fetch_interval', 30);
    }

    /**
     * Log successful sync with statistics.
     */
    protected function logSuccess(array $stats): void
    {
        $message = sprintf(
            'Articles synced successfully. Processed: %d, Successful: %d, Failed: %d',
            $stats['total_processed'],
            $stats['successful'],
            $stats['failed']
        );

        Log::info($message, [
            'job_id' => $this->job->getJobId(),
            'attempt' => $this->attempts(),
            'stats' => $stats,
        ]);
    }

    /**
     * Log sync error.
     */
    protected function logError(Exception $e): void
    {
        Log::error('Error syncing articles', [
            'job_id' => $this->job->getJobId(),
            'attempt' => $this->attempts(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}