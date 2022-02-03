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
            $userAttLogs = $this->attendanceLog->getUserAttendanceLogsByDate($data->user_id, $data->attendance_date);
            $hours = $this->calculateHours($userAttLogs, $response);
            echo '<pre>'; print_r($hours); exit;
            $attData =  [
                'user_id' => $data->user_id,
                'attendance_date' => gmdate('Y-m-d', strtotime($data->attendance_date)),
                'last_attendance_status' => $data->attendance_status,
                'last_attendance_id' => $data->id,
                'hours' => $hours,
                'status' => 'A',
                // user who created it
                'created_by' => 1,
                // user who modify this row
                'last_modified_by' => 1,
                'updated_at' => '2022-02-01',
                'created_at' => '2022-02-01',
                'deleted_at' => 'NULL',
            ];

        }
        catch (Exception $e) {
            $response['data'] = $e->getMessage();
        } finally {
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
                $checkin = new DateTime($checkinLogs[$i]['attendance_status_date']);
                $checkout = new DateTime($checkoutLogs[$i]['attendance_status_date']);
                $interval = $checkin->diff($checkout);
                $diffHours = $interval->h;
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
