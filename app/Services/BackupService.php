<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupService
{
    /**
     * Generate backup and return the path to the file
     *
     * @return array [path, filename]
     */
    public function generateBackup(): array
    {
        $driver = DB::getDriverName();
        $timestamp = date('Y-m-d_H-i-s');

        if ($driver === 'sqlite') {
            $filename = "backup_library_{$timestamp}.sqlite";
            $originalPath = config('database.connections.sqlite.database');

            if (!file_exists($originalPath)) {
                throw new \Exception("Database file not found at $originalPath");
            }

            // Copy to temp to avoid locking and allow download
            $tempPath = sys_get_temp_dir() . '/' . $filename;
            copy($originalPath, $tempPath);

            return [$tempPath, $filename];
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $filename = "backup_library_{$timestamp}.sql";
            $tempPath = sys_get_temp_dir() . '/' . $filename;

            $this->dumpMySql($tempPath);

            return [$tempPath, $filename];
        }

        throw new \Exception("Backup driver '$driver' not supported.");
    }

    protected function dumpMySql(string $path)
    {
        $file = fopen($path, 'w');
        fwrite($file, "-- Library Management System Backup\n");
        fwrite($file, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
        fwrite($file, "-- --------------------------------------------------------\n\n");

        // Get all tables
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        // Disable foreign key checks
        fwrite($file, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($tables as $table) {
            // Drop table
            fwrite($file, "DROP TABLE IF EXISTS `$table`;\n");

            // Create table
            $createTableResult = DB::select("SHOW CREATE TABLE `$table`");
            $createTable = $createTableResult[0]->{'Create Table'} ?? $createTableResult[0]->{'CREATE TABLE'};
            fwrite($file, $createTable . ";\n\n");

            // Insert data
            // Use cursor to minimize memory usage
            foreach (DB::table($table)->cursor() as $row) {
                $values = array_map(function ($value) {
                    if (is_null($value)) return "NULL";
                    // Escape special characters for SQL
                    $value = str_replace("\\", "\\\\", $value);
                    $value = str_replace("'", "\'", $value);
                    $value = str_replace("\r", "\\r", $value);
                    $value = str_replace("\n", "\\n", $value);
                    return "'" . $value . "'";
                }, (array) $row);

                fwrite($file, "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n");
            }
            fwrite($file, "\n");
        }

        fwrite($file, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($file);
    }
}
