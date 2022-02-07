<?php

namespace Insyghts\Hubstaff\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Insyghts\Hubstaff\Events\AttendanceLogCreated;

class AttendanceLogListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(AttendanceLogCreated $attLog)
    {
        Log::info($attLog->attendanceLog);
    }
}
