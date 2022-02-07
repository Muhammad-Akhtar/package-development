<?php

namespace Insyghts\Hubstaff\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Insyghts\Hubstaff\Services\ActivityScreenShotService;
use Insyghts\Hubstaff\Services\ActivityLogService;

class ActivitiesController extends Controller
{
    public function __construct(ActivityLogService $aLog, 
                                ActivityScreenShotService $aScreenShot)
    {
        $this->actLogService = $aLog;
        $this->actScreenShotService = $aScreenShot;
    }

    public function store(Request $request)
    {
        // Data with a zip file.
        $input = $request->all();
        $result = $this->actLogService->saveActivityLog($input);
        if($result['success']){
            // here save screenshot saving
        }else{
            return response()->json(['success' => false, 'message' => $result['data']]);
        }
    }
}
