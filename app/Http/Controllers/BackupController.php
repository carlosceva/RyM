<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Storage::disk('local')->files('backups');
        return view('Administracion.sistema.index', compact('backups'));
    }

    public function realizarBackup()
    {
        try {
            $connection = config('database.default');
            $db = config("database.connections.$connection");

            $usuario = $db['username'];
            $password = $db['password'];
            $host = $db['host'] ?? 'localhost';
            $puerto = $db['port'] ?? 3306;
            $base = $db['database'];

            $fecha = now()->format('Y_m_d_H_i_s');
            $nombreArchivo = "backup_{$connection}_{$fecha}.sql";
            $rutaBackup = storage_path("app/backups/{$nombreArchivo}");

            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0777, true);
            }

            // Escapar los argumentos
            $usuarioArg = '--user=' . escapeshellarg($usuario);
            $passwordArg = '--password=' . escapeshellarg($password);
            $hostArg = '--host=' . escapeshellarg($host);
            $portArg = '--port=' . escapeshellarg($puerto);
            $baseArg = escapeshellarg($base);
            $rutaBackupArg = escapeshellarg($rutaBackup);

            $command = "mysqldump {$usuarioArg} {$passwordArg} {$hostArg} {$portArg} {$baseArg} > {$rutaBackupArg} 2>&1";

            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                throw new \Exception("Backup falló. Código de salida: $return_var. Output: " . implode("\n", $output));
            }

            return response()->download($rutaBackup)->deleteFileAfterSend();
        } catch (\Exception $e) {
            Log::error('Error al generar respaldo: ' . $e->getMessage());
            return back()->with('error', 'Error al generar respaldo: ' . $e->getMessage());
        }
    }

    public function restaurarBackup(Request $request)
    {
        try {
            $request->validate([
                'backup_file' => 'required|file|mimes:sql,txt',
            ]);

            $uploadedFile = $request->file('backup_file');
            $backupDir = storage_path('app/backups');

            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $uploadedFile->move($backupDir, 'tmp_restore.sql');
            $backupFilePath = $backupDir . DIRECTORY_SEPARATOR . 'tmp_restore.sql';

            $connection = config('database.default');
            $db = config("database.connections.$connection");

            $usuario = $db['username'];
            $password = $db['password'];
            $host = $db['host'] ?? 'localhost';
            $puerto = $db['port'] ?? 3306;
            $base = $db['database'];

            $command = "mysql --user={$usuario} --password=\"{$password}\" --host={$host} --port={$puerto} {$base} < \"{$backupFilePath}\"";

            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                throw new \Exception("Restauración falló. Código de salida: $return_var. Output: " . implode("\n", $output));
            }

            return back()->with('success', 'Base de datos restaurada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al restaurar respaldo: ' . $e->getMessage());
            return back()->with('error', 'Error al restaurar respaldo: ' . $e->getMessage());
        }
    }
}
