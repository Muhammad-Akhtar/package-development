<?php

namespace Insyghts\Hubstaff\Controllers;

use App\Http\Controllers\Controller;
use GrahamCampbell\ResultType\Success;
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
 
        if($result['success']){
            return response()->json(['success' => false, 'attendances' => $result['data']]);        
        }else{
            return response()->json(['success' =>false, 'message' => $result['data']]);
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
            return response()->json(['success' => true, 'data' => $result['data']]);
        }else{
            return response()->json(['success' => false, 'message' => $result['data']]); exit;
        }
    }

    public function show($id)
    {
        $result = $this->attendanceService->getAttendanceById($id);
        if($result['success']){
            return response()->json(['success' => true, 'data' => $result['data']]);
        }else{
            return response()->json(['success' => false, 'message' => $result['data']]);
        }
    }

    public function getAttendanceByUser($id)
    {
        $result = $this->attendanceService->getAttendanceByUser($id);
        if($result['success']){
            return response()->json(['success' => true, 'data' => $result['data']]);
        }else{
            return response()->json(['success' => false, 'message' => $result['data']]);
        }
    }

    public function getAttendanceByDate($date)
    {
        $result = $this->attendanceService->getAttendanceByDate($date);
        if($result['success']){
            return response()->json([ 'success' => true, 'data' => $result['data'] ]);
        }else{
            return response()->json([ 'success' => false, 'message' => $result['data']]);
        }   
    }

    public function getAttendanceByUserAndDate(Request $request)
    {
        $user_id = $request->user_id;
        $attendance_date = $request->attendance_date;
        $result = $this->attendanceService->getAttendanceByUserAndDate($user_id, $attendance_date);
        if($result['success']){
            return response()->json(['success' => true, 'data' => $result['data']]);
        }else{
            return response()->json(['success' => false, 'message' => $result['data']]);
        }
    }
}
