<?php

namespace App\Console\Commands\Utility;

use App\Models\Tag;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Console\Command;

class UpdateTagLocaleToEnglish extends Command
{
    protected $signature = 'cloudradar:tags:update-locale-to-en';

    public function handle()
    {
        $this->info('Updating all Tags to use EN locale...');
        TenantManager::disableTenancyChecks();

        $query = Tag::query()->withoutGlobalScopes();

        $count = $query->count();
        $this->info($count." tag(s) to update...");

        $this->output->progressStart($count);
        $query->cursor()
            ->each(function (Tag $tag) {
                $name = array_values($tag->getTranslations('name'))[0];
                $tag->replaceTranslations('name', ['en' => $name]);
                $slug = array_values($tag->getTranslations('slug'))[0];
                $tag->replaceTranslations('slug', ['en' => $slug]);
                $tag->save();
                $this->output->progressAdvance();
            });

        $this->output->progressFinish();
        $this->info('Tags updated.');
    }
}
