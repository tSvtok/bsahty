<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;

class EventController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Event::with('questions');

        if ($request->has('sport')) {
            $query->where('sport', $request->sport);
        }

        $events = $query->latest()->paginate(15);
        return response()->json(['data' => $events]);
    }

    public function store(StoreEventRequest $request)
    {
        $validated = $request->validated();
        $validated['organizer_id'] = $request->user()->id;
        
        $event = Event::create($validated);
        $event->load('organizer');

        broadcast(new \App\Events\EventCreated($event))->toOthers();

        if ($request->filled('question_id')) {
            $question = \App\Models\Question::find($request->question_id);
            if ($question) {
                $question->update(['event_id' => $event->id]);
            }
        }

        return response()->json(['data' => $event], 201);
    }

    public function show(Event $event)
    {
        $event->load(['questions.user', 'participants', 'organizer']);
        return response()->json(['data' => $event]);
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        if ($request->has('join')) {
            $user = $request->user();
            if ($request->join) {
                $event->participants()->syncWithoutDetaching([$user->id]);
            } else {
                $event->participants()->detach($user->id);
            }
            
            event(new \App\Events\EventParticipantsUpdated($event));

            return response()->json(['data' => $event->load('participants')]);
        }

        $event->update($request->validated());
        return response()->json(['data' => $event]);
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(null, 204);
    }
}
