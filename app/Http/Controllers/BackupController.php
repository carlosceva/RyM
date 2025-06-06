<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

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
            $db = config('database.connections.pgsql');
            $usuario = $db['username'];
            $password = $db['password'];
            $host = $db['host'] ?? 'localhost';
            $puerto = $db['port'] ?? 5432;
            $base = $db['database'];

            // Asegúrate de tener correctamente seteado el path de pg_dump
            $pgDumpPath = '"C:\\Program Files\\PostgreSQL\\16\\bin\\pg_dump.exe"';

            $fecha = now()->format('Y_m_d_H_i_s');
            $nombreArchivo = "backup_{$fecha}.sql";
            $rutaBackup = storage_path("app/backups/{$nombreArchivo}");

            // Creamos la carpeta si no existe
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0777, true);
            }

            putenv("PGPASSWORD=$password");

            $command = "$pgDumpPath --clean --if-exists -U $usuario -h $host -p $puerto -d $base -F p -f \"$rutaBackup\"";

            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                throw new \Exception("pg_dump falló. Output: " . implode("\n", $output));
            }

            return back()->with('success', 'Respaldo generado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al generar respaldo: ' . $e->getMessage());
            return back()->with('error', 'Error al generar respaldo.');
        }
    }

    public function restaurarBackup(Request $request)
    {
        try {
            $request->validate([
                'backup_file' => 'required|file|mimes:sql,txt',
            ]);

            // Guardar archivo subido
            $uploadedFile = $request->file('backup_file');

            // Asegura que la carpeta backups existe
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Guardar con nombre temporal fijo para restaurar
            $uploadedFile->move($backupDir, 'tmp_restore.sql');

            $backupFilePath = $backupDir . DIRECTORY_SEPARATOR . 'tmp_restore.sql';

            // Obtener config de base de datos
            $db = config('database.connections.pgsql');
            $usuario = $db['username'];
            $password = $db['password'];
            $host = $db['host'] ?? 'localhost';
            $puerto = $db['port'] ?? 5432;
            $base = $db['database'];

            // Ejecutar comando de restauración con psql
            putenv("PGPASSWORD=$password");
            $psqlPath = '"C:\\Program Files\\PostgreSQL\\16\\bin\\psql.exe"';

            $command = "$psqlPath -U $usuario -h $host -p $puerto -d $base -f \"$backupFilePath\"";

            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                throw new \Exception("psql falló. Output: " . implode("\n", $output));
            }

            return back()->with('success', 'Base de datos restaurada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al restaurar respaldo: ' . $e->getMessage());
            return back()->with('error', 'Error al restaurar respaldo.');
        }
    }
}

