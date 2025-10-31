<?php
namespace App\Jobs;

use App\Services\FacebookCapiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFacebookCapiEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payload;
    public $pixelId;
    public $accessToken;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pixelId, $accessToken, array $payload)
    {
        $this->pixelId = $pixelId;
        $this->accessToken = $accessToken;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FacebookCapiService $service)
    {
        if (empty($this->pixelId) || empty($this->accessToken)) {
            Log::warning('Facebook CAPI job skipped: missing pixelId or accessToken');
            return;
        }

        $result = $service->sendEvent($this->pixelId, $this->accessToken, $this->payload);
        Log::info('Facebook CAPI job result', ['result' => $result]);
    }
}
