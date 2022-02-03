<?php

namespace Insyghts\Hubstaff\Services;

use App\Models\User;
use DateTime;
use Exception;
use Insyghts\Hubstaff\Models\Attendance;
use Insyghts\Hubstaff\Models\AttendanceLog;

class AttendanceService
{
    function __construct(Attendance $attendance,
                        AttendanceLog $attendanceLog)
    {
        $this->attendance = $attendance;
        $this->attendanceLog = $attendanceLog;
    }

    public function getAttendanceList($filters=[])
    {
        $response = [
            'success' => 0,
            'data'    => "There is some error while processing your request",
        ];
        try{
            $data = $this->attendance->getAllAttendances($filters);     
            if($data && count($data) > 0){
                $response['success'] = 1;
                $response['data']    = $data;
            }else{
                $response['data'] = "No records found in database";
            }
        }catch (Exception $e) {
			$response = [
				'success' => 0,
				'data' => $e->getMessage(),
			];
		} finally {			
			return $response;
		}	

    }
    public function saveAttendance($data)
    {
        $response = [
            'success' => 0,
            'data' => 'There is some error while saving'
        ];
        try{
            // to calculate hours below these two functions can be used
            $userAttLogs = $this->attendanceLog->getUserAttendanceLogsByDate($data->user_id, $data->attendance_date);
            $hours = $this->calculateHours($userAttLogs, $response);
            // hours calculation done.
            $attendanceDate = gmdate('Y-m-d', strtotime($data->attendance_date));
            $attData =  [
                'user_id' => $data->user_id,
                'attendance_date' => $attendanceDate,
                'last_attendance_status' => $data->attendance_status,
                'last_attendance_id' => $data->id,
                'hours' => $hours,
                'status' => 'A',
                // user who created it
                'created_by' => 1,
                // user who modify this row
                'last_modified_by' => 1,
            ];
            $attendance = $this->attendance->getByDateAndUser($data->user_id, $attendanceDate);
            if($attendance == null){
                $attendance = new Attendance();
                // Creation
                $attendance->user_id = $attData['user_id'];
                $attendance->attendance_date = $attData['attendance_date'];
                $attendance->last_attendance_status = $attData['last_attendance_status'];
                $attendance->last_attendance_id = $attData['last_attendance_id'];
                $attendance->hours = $attData['hours']; 
                $attendance->status = $attData['status']; 
                $attendance->created_by = $attData['created_by']; 
                $attendance->last_modified_by = NULL;
                $attendance->deleted_by = NULL;
            }else{
                // Update or Modification
                $attendance->last_attendance_status = $attData['last_attendance_status'];
                $attendance->last_attendance_id = $attData['last_attendance_id'];
                $attendance->hours = $attData['hours']; 
                $attendance->last_modified_by = $attData['last_modified_by'];
            }
            $inserted = $this->attendance->saveAttendance($attendance);
            if($inserted){
                
            }
        }
        catch (Exception $e) {
            $response['data'] = $e->getMessage();
        } finally {
            echo '<pre>'; print_r($response); exit;
            return $response;
        }
        
    }

    public function calculateHours($userAttLogs, &$response)
    {
        $hours = 0;
        $checkinLogs = $userAttLogs['checkin_logs'];
        $checkoutLogs = $userAttLogs['checkout_logs'];
        $checkinCount = count($checkinLogs);
        $checkoutCount = count($checkoutLogs); 
        if( $checkinCount > 0  && $checkoutCount > 0){
            $loopCount = $checkoutCount;
            for($i=0; $i < $loopCount; $i++){
                // $checkin = new DateTime($checkinLogs[$i]['attendance_status_date']);
                // $checkout = new DateTime($checkoutLogs[$i]['attendance_status_date']);
                // $interval = $checkout->diff($checkin);
                // $diffHours = $interval->h;
                $cin=strtotime($checkinLogs[$i]['attendance_status_date']);
                $cout=strtotime($checkoutLogs[$i]['attendance_status_date']);
                $d = abs($cout - $cin);
                $diffHours= $d/(60 * 60);
                $hours += $diffHours;
            } 
        }
        return $hours;
    }

    public function getAttendanceById($id)
    {
        return "Attendance services returned";
    }

    public function updateAttendance($update, $id)
    {

    }

    public function deleteAttendance($id)
    {

    }
}
