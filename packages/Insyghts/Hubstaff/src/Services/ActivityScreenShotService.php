<?php

namespace Insyghts\Hubstaff\Services;

use Insyghts\Hubstaff\Models\ActivityLog;
use Insyghts\Hubstaff\Models\ActivityScreenShot;

class ActivityScreenShotService
{
    function __construct(ActivityLog $aLog,
                        ActivityScreenShot $aScreenShot)
    {
        $this->aLog = $aLog;
        $this->aScreenShot = $aScreenShot;
    }
}