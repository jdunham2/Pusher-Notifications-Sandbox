<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notification');
    }

    public function notify(Request $request)
    {
        $notifyText = e($request->input('notify_text'));
        $socketId = e($request->input('socket_id'));

        // TODO: Get Pusher instance from service container
        $pusher = App::make('pusher');

        $pusher->set_logger(new LaravelLoggerProxy());

        // TODO: The notification event data should have a property named 'text'
        $eventData = array('message' => $notifyText);

        // TODO: On the 'notifications' channel trigger a 'new-notification' event
        $pusher->trigger('notifications', 'new-notification', $eventData, $socketId);
    }
}

class LaravelLoggerProxy
{
    public function log($msg)
    {
        Log::info($msg);
    }
}
