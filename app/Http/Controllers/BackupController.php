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
        // $connection = config('database.default');
        // dd($connection);

        try {
            $connection = config('database.default');
            $db = config("database.connections.$connection");
            $usuario = $db['username'];
            $password = $db['password'];
            $host = $db['host'] ?? 'localhost';
            $puerto = $db['port'] ?? ($connection === 'pgsql' ? 5432 : 3306);
            $base = $db['database'];

            $fecha = now()->format('Y_m_d_H_i_s');
            $nombreArchivo = "backup_{$connection}_{$fecha}.sql";
            $rutaBackup = storage_path("app/backups/{$nombreArchivo}");

            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0777, true);
            }

            if ($connection === 'pgsql') {
                // ---------- POSTGRES ----------
                $pgDumpPath = '"C:\\Program Files\\PostgreSQL\\16\\bin\\pg_dump.exe"';
                putenv("PGPASSWORD=$password");

                $command = "$pgDumpPath --clean --if-exists -U $usuario -h $host -p $puerto -d $base -F p -f \"$rutaBackup\"";
            } elseif ($connection === 'mysql') {
                // ---------- MYSQL ----------
                $mysqldumpPath = '"C:\\xampp\\mysql\\bin\\mysqldump.exe"'; // cambia ruta según tu instalación
                $command = "$mysqldumpPath --user=$usuario --password=$password --host=$host --port=$puerto $base > \"$rutaBackup\"";
            } else {
                throw new \Exception("Driver de base de datos no soportado: $connection");
            }

            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                throw new \Exception("Backup falló. Output: " . implode("\n", $output));
            }

            return response()->download($rutaBackup)->deleteFileAfterSend();

        } catch (\Exception $e) {
            Log::error('Error al generar respaldo: ' . $e->getMessage());
            return back()->with('error', 'Error al generar respaldo.');
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
            $puerto = $db['port'] ?? ($connection === 'pgsql' ? 5432 : 3306);
            $base = $db['database'];

            if ($connection === 'pgsql') {
                // ---------- POSTGRES ----------
                putenv("PGPASSWORD=$password");
                $psqlPath = '"C:\\Program Files\\PostgreSQL\\16\\bin\\psql.exe"';
                $command = "$psqlPath -U $usuario -h $host -p $puerto -d $base -f \"$backupFilePath\"";
            } elseif ($connection === 'mysql') {
                // ---------- MYSQL ----------
                $mysqlPath = '"C:\\xampp\\mysql\\bin\\mysql.exe"'; // cambia ruta según tu instalación
                $command = "$mysqlPath --user=$usuario --password=$password --host=$host --port=$puerto $base < \"$backupFilePath\"";
            } else {
                throw new \Exception("Driver de base de datos no soportado: $connection");
            }

            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                throw new \Exception("Restauración falló. Output: " . implode("\n", $output));
            }

            return back()->with('success', 'Base de datos restaurada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al restaurar respaldo: ' . $e->getMessage());
            return back()->with('error', 'Error al restaurar respaldo.');
        }
    }
}
