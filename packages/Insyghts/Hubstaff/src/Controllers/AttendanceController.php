<?php

namespace Insyghts\Hubstaff\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Insyghts\Hubstaff\Services\AttendanceLogService;
use Insyghts\Hubstaff\Services\AttendanceService;

class AttendanceController extends Controller
{
    public function __construct(AttendanceService $attendanceService,
                                AttendanceLogService $attendanceLogService)
    {
        $this->attendanceService = $attendanceService;
        $this->attendanceLogService = $attendanceLogService;
    }

    public function index(Request $request)
    {
        $filters = $request->all();
        $result = $this->attendanceService->getAttendanceList($filters);
        echo '<pre>'; print_r($result); exit;
        if($result['success']){
            $data = $result['data'];
            // return View('attendance.index', compact('data','filters'));
            echo '<pre>'; print_r($data); exit;        
        }else{
            // return redirect()->back()->withInput()->with('class', 'alert alert-danger')->with('message', $result['data']);
            echo '<pre>'; print_r(["message" => $result['data']]); exit;
        }
    }

    // Attendance Log will be received here
    public function store(Request $request)
    {
        $input = $request->all();
        $result = $this->attendanceLogService->saveAttendanceLog($input);
        if($result['success']){
            $attLog = $result['data'];
            $result = $this->attendanceService->saveAttendance($attLog);
        }else{
            echo '<pre>'; print_r(['message' => $result['data']]); exit;
        }
    }
}
