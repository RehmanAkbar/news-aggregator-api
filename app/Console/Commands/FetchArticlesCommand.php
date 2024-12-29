<?php

namespace App\Console\Commands;

use App\Jobs\SyncArticlesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch {--force : Force fetch articles regardless of last fetch time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch job to fetch articles from configured news sources';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $force = $this->option('force');
            
            $this->info('Dispatching article sync job...');
            
            // Dispatch the job
            SyncArticlesJob::dispatch($force);
            
            $this->info('Article sync job has been dispatched successfully');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $message = "Error dispatching article sync job: {$e->getMessage()}";
            $this->error($message);
            Log::error($message);
            
            return Command::FAILURE;
        }
    }
}