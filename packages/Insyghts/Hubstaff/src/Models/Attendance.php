<?php

namespace Insyghts\Hubstaff\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public function getAttendanceList($filter)
    {
        $queryObj = $this;
        $limit = !empty($filter['limit']) ? $filter['limit'] : 30;
        if(count($filter)){
            // query with where clause
        }
        $result = $queryObj->orderBy('id', 'DESC')->paginate($limit);
        return $result;
    }

    public function saveAttendance($attendance)
    {
        $inserted=false;
         
        $inserted = $attendance->save();

        return $inserted;
    }

    public function getAttendanceById($id)
    {
        return Attendance::find($id);
    }

    public function getAttendanceByUser($user_id)
    {
        return Attendance::where('user_id', '=', ((int)$user_id))->get();
    }

    public function getAttendanceByDate($attendance_date)
    {
        return Attendance::where('attendance_date', '=', gmdate('Y-m-d', strtotime($attendance_date)))
                ->get();
    }

    public function getAttendanceByUserAndDate($user_id, $attendance_date){
        return Attendance::where('user_id', '=', $user_id)
                    ->where('attendance_date', $attendance_date)->first();
    }
}
