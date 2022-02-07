<?php

namespace Insyghts\Hubstaff\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class AttendanceLog extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'attendance_logs';
  
    public function getPreviousEntry($entry)
    {
        $previousEntry = AttendanceLog::where('user_id', '=', ((int)$entry['user_id']))
            ->orderBy('id', 'DESC')->first();
        return $previousEntry;
    }
    public function saveRecord($data)
    {
        $inserted = false;
        $inserted = AttendanceLog::insert(
            $data
        );
        if($inserted){
            $inserted = AttendanceLog::latest()->first();
        }
        return $inserted;
    }
    
    public function getUserAttendanceLogsByDate($user_id, $attendance_date)
    {
        $checkInLogs = AttendanceLog::where('user_id','=', $user_id)
                            ->where('attendance_date', '=', gmdate('Y-m-d', strtotime($attendance_date)))
                            ->where('attendance_status', '=', 'I')
                            ->get();
        $checkOutLogs = AttendanceLog::where('user_id','=', $user_id)
                            ->where('attendance_date', '=', gmdate('Y-m-d', strtotime($attendance_date)))
                            ->where('attendance_status', '=', 'O')
                            ->get();
        return [
            'checkin_logs' => $checkInLogs->toArray(), 
            'checkout_logs' => $checkOutLogs->toArray(),    
        ];
    }
}
