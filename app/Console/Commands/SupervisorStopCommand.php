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

class SupervisorStopCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervisor:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Para todos os processos do supervisor';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $guzzleClient = new GuzzleClient([
            'auth' => [
                config('services.supervisor.user'),
                config('services.supervisor.password'),
            ],
        ]);

        $fXmlRpcClient = new fXmlRpcClient(
            'http://127.0.0.1:9001/RPC2',
            new PsrTransport(
                new HttpFactory(),
                $guzzleClient
            )
        );

        $supervisor = new Supervisor($fXmlRpcClient);
        $supervisor->stopAllProcesses();
    }
}
