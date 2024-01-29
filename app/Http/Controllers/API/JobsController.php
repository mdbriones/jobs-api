<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobDetailsRequest;
use App\Services\JobService;
use App\Services\ScrapeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class JobsController extends Controller
{
    public $jobsService;
    public $scrapeService;

    public function __construct(JobService $jobsService, ScrapeService $scrapeService)
    {
        $this->jobsService = $jobsService;
        $this->scrapeService = $scrapeService;
    }

    public function createJob(JobDetailsRequest $request)
    {
        $validatedData = $request->validated();

        // dd($validatedData);

        // dispatch job for scraping of data from sites
        // -- could be JobDataScraping::dispatch();
        $results = $this->scrapeService->scrapeData($validatedData);
        
        $validatedData['status'] = 'completed';
        $validatedData['scrapedData'] = $results;
        
        $this->jobsService->createJobDetails($validatedData);
        // --

        return response()->json(
            [
                'message' => 'Data creation done!', 
            ]
        );
    }

    public function getAllJobs()
    {
        // dd(Redis::dbsize());
        $jobKeys = $this->jobsService->fetchAllJobs();

        return response()->json(
            [
                'keys' => $jobKeys
            ]
        );
    }

    public function getJobDetails($id)
    {
        $details = $this->jobsService->getDetails($id);

        if ($details['status'] === 'success') {
            return response()->json(['data' => $details]);
        }
        
        return response()->json(['message' => 'Job details not found'], http_response_code(404));
    }

    public function deleteAllKeys()
    {
        $this->jobsService->deleteAllData();

        return response()->json(
            [
                'message' => 'All records deleted successfully'
            ]
        );
    }

    public function deleteKey($id)
    {
        $this->jobsService->deleteAllData($id);

        return response()->json(
            [
                'message' => 'Key deleted successfully'
            ]
        );
    }
}
