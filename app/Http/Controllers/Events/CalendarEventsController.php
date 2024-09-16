<?php

namespace App\Http\Controllers\Events;

use App\Models\Events;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalendarEventsController extends Controller
{
    public function getCalendarEvents() {
        $events = Events::all();

        return response()->json($events);
    }
    public function deleteCalendarEvents($id) {
        $event = Events::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully!']);
    }

    public function updateCalendarEvents(Request $request, $id) {
        $event = Events::findOrFail($id);
        $event->update([
            'start' => Carbon::parse($request->input('start_date'))->setTimezone('UTC'),
            'end' => Carbon::parse($request->input('end_date'))->setTimezone('UTC')
        ]);

        return response()->json(['message' => 'Event re-assigned successfully!']);
    }

    public function resizeEvent(Request $request, $id) {
        $event = Events::findOrFail($id);
        $newEndDate = Carbon::parse($request->input('end_date'))->setTimezone('UTC');
        $event->update(['end' => $newEndDate]);

        return response()->json(['message' => 'Event resized']);
    }

    public function searchEvent(Request $request) {
        $searchEvent = $request->input('eventTitle');
        $eventMatch = Events::where('eventTitle', 'like', '%'. $searchEvent .'%')->get();

        return response()->json($eventMatch);
    }  
}
