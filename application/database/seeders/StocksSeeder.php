<?php
declare(strict_types=1);

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use App\Services\WbApiService;

class StocksSeeder extends Seeder
{
    const SOURCE_NAME = 'stocks';

    /**
     * @var WbApiService
     */
    private $apiService;

    /**
     * @param WbApiService $apiService
     */
    public function __construct(WbApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $queryParams = [
            'dateFrom' => now()->format("Y-m-d")
        ];

        $this->apiService->importData(self::SOURCE_NAME, $queryParams);
    }
}
