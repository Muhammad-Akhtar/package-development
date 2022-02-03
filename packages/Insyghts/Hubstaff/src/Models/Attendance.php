<?php

namespace Insyghts\Hubstaff\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public function getAllAttendances($filter)
    {
        $queryObj = $this;
        $limit = !empty($filter['limit']) ? $filter['limit'] : 30;
        if(isset($filter['any_filter'])){
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

    public function getByDateAndUser($user_id, $attendance_date){
        return Attendance::where('user_id', '=', $user_id)
                    ->where('attendance_date', $attendance_date)->first();
    }
}
