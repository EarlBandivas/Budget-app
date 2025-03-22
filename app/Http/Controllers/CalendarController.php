<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    protected $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index()
    {
        try {
            $events = $this->calendarService->listEvents([
                'timeMin' => Carbon::now()->startOfMonth()->format('c'),
                'timeMax' => Carbon::now()->endOfMonth()->format('c'),
                'singleEvents' => true,
                'orderBy' => 'startTime',
            ]);

            return inertia('Calendar/Index', [
                'events' => $events
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch calendar events.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'attendees' => 'nullable|array',
            'attendees.*' => 'email',
        ]);

        try {
            $event = $this->calendarService->createEvent(
                $validated['title'],
                $validated['description'],
                Carbon::parse($validated['start_datetime']),
                Carbon::parse($validated['end_datetime']),
                $validated['attendees'] ?? []
            );

            return back()->with('success', 'Event created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create event.');
        }
    }

    public function update(Request $request, $eventId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'attendees' => 'nullable|array',
            'attendees.*' => 'email',
        ]);

        try {
            $event = $this->calendarService->updateEvent(
                $eventId,
                $validated['title'],
                $validated['description'],
                Carbon::parse($validated['start_datetime']),
                Carbon::parse($validated['end_datetime']),
                $validated['attendees'] ?? []
            );

            return back()->with('success', 'Event updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update event.');
        }
    }

    public function destroy($eventId)
    {
        try {
            $this->calendarService->deleteEvent($eventId);
            return back()->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete event.');
        }
    }

    public function show($eventId)
    {
        try {
            $event = $this->calendarService->getEvent($eventId);
            return inertia('Calendar/Show', [
                'event' => $event
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch event details.');
        }
    }
}