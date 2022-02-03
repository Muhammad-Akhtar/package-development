<?php

use Insyghts\Hubstaff\Controllers\AttendanceController;

Route::get('hubstaff', function(){
	echo 'Hello from the hubstaff package!';
});

Route::post('hubstaff/attendances/filter', [AttendanceController::class, 'index']);
Route::get('hubstaff/attendances', [AttendanceController::class, 'index']);
Route::post('hubstaff/attendance/save', [AttendanceController::class, 'store']);