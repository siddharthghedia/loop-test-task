<?php

namespace App\Console\Commands;

use App\Imports\CustomersImport;
use App\Imports\ProductsImport;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CSV file data to API Webshop database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Excel::import(new CustomersImport, storage_path('csv/customers.csv'));

        Log::info("Total Customers imported to API Webshop: ".Customer::count());

        \Excel::import(new ProductsImport, storage_path('csv/products.csv'));

        Log::info("Total Products imported to API Webshop: ".Product::count());

        return Command::SUCCESS;
    }
}
