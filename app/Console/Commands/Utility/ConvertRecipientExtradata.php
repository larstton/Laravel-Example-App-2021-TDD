<?php

namespace App\Console\Commands\Utility;

use App\Models\Recipient;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Console\Command;

class ConvertRecipientExtradata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudradar:recipients:convert-extradata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts recipient extra_data into new format';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        TenantManager::disableTenancyChecks();

        collect(['sms', 'phonecall', 'pushover', 'integromat'])->each(function ($mediaType) {
            $this->info(sprintf("Converting %s extra_data", $mediaType));

            Recipient::query()
                ->withoutGlobalScopes()
                ->whereMediaType($mediaType)
                ->cursor()
                ->each(function (Recipient $recipient) use ($mediaType) {
                    $currentData = collect($recipient->extra_data);

                    if ($currentData->offsetExists($mediaType) || $currentData->isEmpty()) {
                        return;
                    }
                    if (is_null($recipient->team)) {
                        return;
                    }
                    $recipient->team->makeCurrentTenant();

                    $recipient->update([
                        'extra_data' => [
                            $mediaType => $currentData->toArray(),
                        ],
                    ]);
                });
        });
        $this->info("Done!");
    }
}
