<?php /** @noinspection ALL */

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use ParaTest\Runners\PHPUnit\Options;
use ParaTest\Runners\PHPUnit\RunnerInterface;
use ParaTest\Runners\PHPUnit\WrapperRunner;
use Symfony\Component\Console\Output\OutputInterface;

class WrapperRunnerWithDrop implements RunnerInterface
{
    private Application $app;

    private WrapperRunner $runner;

    public function __construct(Options $opts, OutputInterface $output)
    {
        $this->options = $opts;
        $this->runner = new WrapperRunner($opts, $output);
    }

    public function run(): void
    {
        $this->runner->run();

        $this->tearDownTestDatabases();
    }

    public function tearDownTestDatabases()
    {
        $this->createApp();

        $driver = $this->app['config']->get('database.default');
        $dbName = $this->app['config']->get("database.connections.{$driver}.database");

        for ($i = 1; $i <= $this->options->processes(); $i++) {
            $this->swapTestingDatabaseConfiguration($driver, $dbName.$i);
            Artisan::call('db:drop');
        }
    }

    private function createApp()
    {
        $this->app = require __DIR__.'/../../bootstrap/app.php';
        $this->app->make(Kernel::class)->bootstrap();
    }

    protected function swapTestingDatabaseConfiguration($driver, $dbName): void
    {
        $this->app['config']->set([
            "database.connections.{$driver}.database" => $dbName,
        ]);
    }

    public function __call($name, $arguments)
    {
        return $this->runner->$name($arguments);
    }

    public function getExitCode(): int
    {
        return $this->runner->getExitCode();
    }
}
