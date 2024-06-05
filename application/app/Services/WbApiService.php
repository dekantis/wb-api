<?php
declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class WbApiService
{
    const WB_API_DELAY = 13;
    const WB_API_REQUESTS_COUNT = 60;
    const DEFAULT_WB_API_QUERY_PARAMS = [
        'limit' => 500,
        'dateFrom' => '0000-01-01',
    ];

    /**
     * @param string $url
     * @param int $page
     * @return array|null
     */
    private function getDataFromAPI(string $url, int $page = 1): ?array
    {
        $response = Http::get($url . '&page=' . $page);

        if ($response->successful()) {
            return $response->json()['data'];
        }

        return null;
    }

    /**
     * @param string $source
     * @return string
     * @throws Exception
     */
    private function getEndpoint(string $source): string
    {
        $protocol = env('WB_API_PROTOCOL', 'http');
        $url = env('WP_API_URL', '');
        $port = env('WB_API_PORT', '');
        $key = env('WB_API_KEY', '');

        if (empty($port) || empty($url) || empty($key)) {
            throw new Exception('WB_API_URL, WP_API_PORT, WB_API_KEY must be set. Check your .env variables.');
        }

        return "$protocol://$url:$port/api/$source?key=$key";
    }

    /**
     * @param array $queryParams
     * @param string $source
     * @return string
     * @throws Exception
     */
    private function getRequest(array $queryParams, string $source): string
    {
        $url = $this->getEndpoint($source);

        foreach ($queryParams as $key => $value) {
            $url .= '&' . $key . '=' . $value;
        }

        return $url;
    }

    /**
     * @param string $source
     * @param array $data
     * @return bool
     */
    private function saveData(string $source, array $data): bool
    {
        return DB::table($source)->insert($data);
    }

    /**
     * @param string $source
     * @param array $queryParams
     * @return void
     * @throws Exception
     */
    public function importData(string $source, array $queryParams = []): void
    {
        $this->wbApiDelayTimer(self::WB_API_DELAY);

        echo PHP_EOL;
        echo '--------------------------------------------------' . PHP_EOL;
        echo 'import ' . $source . ' started!' . PHP_EOL;
        echo PHP_EOL;

        $currentPage = 1;
        $params = array_merge(self::DEFAULT_WB_API_QUERY_PARAMS, $queryParams);

        $url = $this->getRequest($params, $source);

        do {
            $data = $this->getDataFromAPI($url, $currentPage);

            if (is_null($data)) {
                echo 'Something went wrong!' . PHP_EOL;
                break;
            }

            $count = count($data);

            if (!$count) {
                echo 'import ' . $source . ' done!' . PHP_EOL;
                echo PHP_EOL;
                echo '--------------------------------------------------' . PHP_EOL;
                echo PHP_EOL;
                break;
            }

            if ($this->saveData($source, $data)) {
                echo $count . ' records successfully added for ' . $source . ' (page ' . $currentPage . ')' . PHP_EOL;
            } else {
                var_dump($data);
                die();
            }

            if ($currentPage % self::WB_API_REQUESTS_COUNT === 0 ) {
                $this->wbApiDelayTimer(self::WB_API_DELAY);
            }

            $currentPage++;

        } while ($count > 0);
    }

    /**
     * @param int $seconds
     * @return void
     */
    private function wbApiDelayTimer(int $seconds): void
    {
        echo 'Just wait ' . $seconds . ' seconds...' . PHP_EOL;
        for ($i = $seconds; $i > 0; $i--) {
            echo "\r$i";
            sleep(1);
        }

        echo "\r";
    }
}