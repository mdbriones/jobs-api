<?php
namespace App\Services;

use Illuminate\Support\Facades\Redis;

class JobService {
    
    public $redis;
    public $redisPrefix;

    public function __construct()
    {
        $this->redis = Redis::connection();
        $this->redisPrefix = 'data';
    }

    public function createJobDetails($data)
    {
        $key = 'data:' . uniqid();
        $this->redis->set($key, json_encode($data));
        
        return $key;
    }

    public function fetchAllJobs()
    {
        $jobKeys = $this->redis->keys($this->redisPrefix . ':*');

        return $jobKeys;
    }

    public function getDetails($id)
    {
        $storedData = $this->redis->get($this->redisPrefix . ':' . $id);

        if ($storedData) {
            $decodedData = json_decode($storedData, true);

            return [
                'status' => 'success',
                'data' => $decodedData,
            ];
            return response()->json(['data' => $decodedData]);
        } else {
            return response()->json(['message' => 'Job details not found'], http_response_code(404));
        }
    }

    public function deleteAllData()
    {
        return $this->redis->flushdb();
    }

    public function deleteRecord($id)
    {
        return $this->redis->del($this->redisPrefix . ':' . $id);
    }
}