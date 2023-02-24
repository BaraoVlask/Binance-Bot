<?php

namespace App\Console\Commands;

use fXmlRpc\Client as fXmlRpcClient;
use fXmlRpc\Transport\PsrTransport;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Console\Command;
use Supervisor\ProcessStates;
use Supervisor\ServiceStates;
use Supervisor\Supervisor;

class SupervisorListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervisor:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista os processos do supervisor';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $data = collect(
            (new Supervisor(
                new fXmlRpcClient(
                    'http://127.0.0.1:9001/RPC2',
                    new PsrTransport(
                        new HttpFactory(),
                        new GuzzleClient([
                            'auth' => [
                                config('services.supervisor.user'),
                                config('services.supervisor.password'),
                            ],
                        ])
                    )
                )
            ))
                ->getAllProcessInfo()
        )
            ->transform(
                fn($arr) => collect($arr)
                    ->forget(['logfile', 'stdout_logfile', 'stderr_logfile'])
            );

        $this->table(
            $data->first()->keys()->toArray(),
            $data->toArray()
        );
    }
}
