<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Supplier;
use Illuminate\Support\Facades\Artisan;

class XmlImportScheduled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml:import:scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run scheduled XML imports for suppliers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get suppliers that need import based on update frequency
        $suppliers = Supplier::where('is_active', true)
            ->whereNotNull('xml_url')
            ->get();

        foreach ($suppliers as $supplier) {
            // Check if import is needed based on update frequency
            if ($this->shouldImport($supplier)) {
                $this->info("Scheduled import for supplier: {$supplier->name}");
                
                try {
                    Artisan::call('xml:import', ['supplier_id' => $supplier->id]);
                    
                    $output = Artisan::output();
                    $this->line($output);
                } catch (\Exception $e) {
                    $this->error("Import failed for supplier {$supplier->name}: " . $e->getMessage());
                }
            }
        }

        return 0;
    }

    /**
     * Check if supplier should be imported
     */
    protected function shouldImport(Supplier $supplier)
    {
        // If never imported, import it
        if (!$supplier->last_import_at) {
            return true;
        }

        // Check update frequency (in hours)
        $hoursSinceLastImport = $supplier->last_import_at->diffInHours(now());
        
        return $hoursSinceLastImport >= $supplier->update_frequency;
    }
}
