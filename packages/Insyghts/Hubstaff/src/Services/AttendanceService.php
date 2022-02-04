<?php

namespace Insyghts\Hubstaff\Services;

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
            'data'    => "There is some error",
        ];
        try{
            $data = $this->attendance->getAttendanceList($filters);     
            if(count($data) > 0){
                $response['success'] = 1;
                $response['data']    = $data->toArray();
            }
        }catch (Exception $e) {
			$show = get_class($e) == 'Illuminate\Database\QueryException' ? false : true;
            if($show){
                $response['data'] = $data['data'];
            }
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
                'created_by' => $data->created_by,
                // user who modify this row
                'last_modified_by' => $data->last_modified_by,
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
                $response['success'] = 1;
                $response['data'] = $attendance;
            }
        }
        catch (Exception $e) {
            $show = get_class($e) == 'Illuminate\Database\QueryException' ? false : true;
            if($show){
                $response['data'] = $e->getMessage();
            }
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
                // $checkin = new DateTime($checkinLogs[$i]['attendance_status_date']);
                // $checkout = new DateTime($checkoutLogs[$i]['attendance_status_date']);
                // $interval = $checkout->diff($checkin);
                // $diffHours = $interval->h;
                $cin=strtotime($checkinLogs[$i]['attendance_status_date']);
                $cout=strtotime($checkoutLogs[$i]['attendance_status_date']);
                $d = abs($cout - $cin);
                $diffHours= round($d/(60 * 60));
                $hours += $diffHours;
            } 
        }
        return $hours;
    }

    
    public function getAttendanceById($id)
    {
        $response = ['success' => 0, 'data' => 'There is some error'];
        try{
            $attendance = $this->attendance->getAttendanceById($id);
            if($attendance){
                $response['success'] = 1;
                $response['data'] = $attendance;
            }
        }catch(Exception $e){
            $show = get_class($e) == 'Illuminate\Database\QueryException' ? false : true;
            if($show){
                $response['data'] = $e->getMessage();
            }
        }finally{
            return $response;
        }
    }

    public function getAttendanceByUser($user_id)
    {
        $response = ['success' => 0, 'data' => "Something went wrong"];
        try{
            $attendances = $this->attendance->getAttendanceByUser($user_id);
            if(count($attendances)){
                $response['success'] = 1;
                $response['data'] = $attendances->toArray();
            }
        }catch(Exception $e){
            $show = get_class($e) == 'Illuminate\Database\QueryException' ? false : true;
            if($show){
                $response['data'] = $e->getMessage();
            }
        }finally{
            return $response;
        }
    }

    public function getAttendanceByDate($attendance_date)
    {
        $response = ['success' => 0, 'data' => "Something went wrong"];
        try{
            $attendances = $this->attendance->getAttendanceByDate($attendance_date);
            if(count($attendances)){
                $response['success'] = 1;
                $response['data'] = $attendances->toArray();
            }
        }catch(Exception $e){
            $show = get_class($e) == 'Illuminate\Database\QueryException' ? false : true;
            if($show){
                $response['data'] = $e->getMessage();
            }   
        }finally{
            return $response;
        }
    }

    public function getAttendanceByUserAndDate($user_id, $attendance_date)
    {
        $response = [
            'success' => 0,
            'data'    => "Something went wrong"
        ];

        try{
            $attendance = $this->attendance->getAttendanceByUserAndDate($user_id, $attendance_date);
            if($attendance){
                $response['success'] = 1;
                $response['data'] = $attendance;
            }
        }catch(Exception $e){
            $show = get_class($e) == "Illuminate\Database\QueryException" ? false : true;
            if($show){
                $response['data'] = $e->getMessage();
            }
        }finally{
            return $response;
        }
    }


    public function updateAttendance($update, $id)
    {

    }

    public function deleteAttendance($id)
    {

    }
}
