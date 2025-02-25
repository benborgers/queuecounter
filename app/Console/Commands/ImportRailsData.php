<?php

namespace App\Console\Commands;

use App\Models\Snapshot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportRailsData extends Command
{
    protected $signature = 'app:import-rails-data';

    public function handle()
    {
        $csvPath = resource_path('rails-export.csv');

        if (! File::exists($csvPath)) {
            $this->error("CSV file not found: {$csvPath}");

            return 1;
        }

        $this->info("Importing data from {$csvPath}...");

        // Open the CSV file
        $file = fopen($csvPath, 'r');

        // Read the header row
        $headers = fgetcsv($file);

        // Check if headers match expected format
        if ($headers !== ['id', 'count', 'created_at', 'updated_at']) {
            $this->error('CSV headers do not match the expected format.');
            fclose($file);

            return 1;
        }

        $importCount = 0;
        $errorCount = 0;

        // Process each row
        while (($row = fgetcsv($file)) !== false) {
            try {
                // Create a new snapshot
                Snapshot::create([
                    'count' => $row[1], // count
                    'timestamp' => $row[2], // using created_at as timestamp
                ]);

                $importCount++;
            } catch (\Exception $e) {
                $this->error("Error importing row {$row[0]}: {$e->getMessage()}");
                $errorCount++;
            }
        }

        fclose($file);

        $this->info("Import completed: {$importCount} records imported, {$errorCount} errors.");

        return 0;
    }
}
