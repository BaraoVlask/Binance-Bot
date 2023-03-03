<?php

namespace App\Console\Commands\Monitor;

use App\Console\Commands\StyledAlertTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class StatusCommand extends Command
{
    use StyledAlertTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mostra a situação da chave de controle do monitor';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (Cache::get('monitorIsRunning', false)) {
            $this->styledAlert('O monitor está rodando!', 'info');
        } else {
            $this->styledAlert('O monitor está parado!', 'fg=red');
        }
    }
}
