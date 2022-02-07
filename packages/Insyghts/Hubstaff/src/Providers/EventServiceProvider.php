<?php

namespace Insyghts\Hubstaff\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Insyghts\Hubstaff\Events\AttendanceLogCreated;
use Insyghts\Hubstaff\Listeners\AttendanceLogListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AttendanceLogCreated::class => [
            AttendanceLogListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}