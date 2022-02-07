<?php

namespace Insyghts\Hubstaff\Services;

use Exception;
use Insyghts\Hubstaff\Events\AttendanceLogSavingEvent;
use Insyghts\Hubstaff\Models\ActivityLog;
use Insyghts\Hubstaff\Models\ActivityScreenShot;
use Insyghts\Hubstaff\Models\AttendanceLog;

class ActivityLogService
{
    function __construct(
        ActivityLog $aLog,
        ActivityScreenShot $aScreenShot,
        AttendanceLog $attendanceLog,
        AttendanceLogService $attendanceLogService
    ) {
        $this->actLog = $aLog;
        $this->actScreenShot = $aScreenShot;
        $this->attendanceLog = $attendanceLog;
        $this->attendanceLogService = $attendanceLogService;
    }

    public function saveActivityLog($data)
    {
        $response = [
            'success' => 0,
            'data' => "There is some error",
        ];
        $activity_date = gmdate('Y-m-d G:i:s', strtotime($data['activity_date']));
        $log_from_date = gmdate('Y-m-d G:i:s', strtotime($data['log_from_date']));
        $log_to_date = gmdate('Y-m-d G:i:s', strtotime($data['log_to_date']));
        $data['activity_date'] = $activity_date;
        $data['log_from_date'] = $log_from_date;
        $dat['log_to_date'] = $log_to_date;
        // $data = [
        //     'user_id' => 1,
        //     'session_token_id' => 1,
        //     'activity_date' => gmdate('Y-m-d G:i:s', strtotime('2022-02-07')),
        //     'log_from_date' => gmdate('Y-m-d G:i:s', strtotime('2022-02-07 10:00:00')),
        //     'log_to_date' => gmdate('Y-m-d G:i:s', strtotime('2022-02-07 18:00:00')),
        //     'note'  =>  'This is test note',
        //     'keyboard_track' => 390,
        //     'mouse_track'   => 1900,
        //     'time_type' => 'CI',
        //     'created_by' => 1,
        //     'last_modified_by' => NULL,
        //     'deleted_by' => NULL,
        // ];

        try {
            // In case of offline data deal with it later
            // if ($data['time_type'] == 'CI') {
            //     $newData = [
            //         'user_id' => $data['user_id'],
            //         'session_token_id' => $data['session_token_id'],
            //         'attendance_date' => $data['activity_date'],
            //         'attendance_status' => 'I',
            //         'attendance_status_date' => $data['log_from_date'],
            //         'status' => 'A',
            //         'created_by' => $data['created_by'],
            //     ];
            //     $isCheckIn = $this->validateActivityLogs($newData, $response);
            //     if($isCheckIn){
            //         // $result = event(new AttendanceLogSavingEvent($newData));
            //         $insertedRecord = $this->attendanceLog->saveRecord($newData);
            //         if($insertedRecord){
            //             // Update Attendance Table too 
            //         }
            //     }
            // } elseif($data['time_type'] == 'CO') {
            //     $newData = [
            //         'user_id' => $data['user_id'],
            //         'session_token_id' => $data['session_token_id'],
            //         'attendance_date' => $data['activity_date'],
            //         'attendance_status' => 'O',
            //         'attendance_status_date' => $data['log_to_date'],
            //         'status' => 'A',
            //         'created_by' => $data['created_by'],
            //     ];
            //     $isCheckOut = $this->validateActivityLogs($newData, $response);
            //     if($isCheckOut){
            //         // Save to Attendance Log and Update Attendance
            //         // Better to dispatch an event here
            //     }
            // }
            

        } catch (Exception $e) {
            $show = get_class($e) == 'Illuminate\Database\QueryException' ? false : true;
            if ($show) {
                $responsep['data'] = $e->getMessage();
            }
        } finally {
            return $response;
        }
    }

    public function validateActivityLogs($data, &$response)
    {
        $valid = false;
        if ($data != null || count($data) > 0) {
            $previousEntry = $this->attendanceLog->getPreviousEntry($data);
            $valid = $this->attendanceLogService->validateConsecutiveEntry($data, $previousEntry, $response);
        }
        return $valid;
    }
}
