<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza dump do banco e rotaciona backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando backup...');

        $filename = 'backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        // Garante diretório
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // Config banco
        $db = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $mysqldump = '"C:\wamp64\bin\mysql\mysql8.3.0\bin\mysqldump.exe"';

        $passwordPart = $pass !== '' ? '-p"' . $pass . '"' : '';

        // Comando mysqldump
        $command = sprintf(
            '"%s" -h%s -u%s %s %s > "%s"',
            $mysqldump,
            $host,
            $user,
            $passwordPart,
            $db,
            $path
        );

        exec($command, $output, $result);

        if ($result !== 0) {
            $this->error('Erro ao gerar dump');
            return 1;
        }

        $this->info('Backup gerado: ' . $filename);

        // ROTAÇÃO (manter 7 dias)
        $this->rotateBackups();

        return 0;
    }

    private function rotateBackups()
    {
        $files = collect(glob(storage_path('app/backups/*.sql')))
            ->sortByDesc(fn($file) => filemtime($file))
            ->values();

        $files->slice(7)->each(function ($file) {
            unlink($file);
            $this->info('Removido backup antigo: ' . basename($file));
        });
    }
}
