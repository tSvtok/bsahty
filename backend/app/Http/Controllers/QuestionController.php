<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['user', 'spot', 'event', 'comments.user', 'reactions']);
        
        if ($request->has('sport_category')) {
            $query->where('sport_category', $request->sport_category);
        }

        // Paginate results (15 per page)
        return response()->json(['data' => $query->latest()->paginate(15)]);
    }

    public function store(StoreQuestionRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $question = Question::create($validated);

        return response()->json(['data' => $question], 201);
    }

    public function show(Question $question)
    {
        $question->load(['user', 'spot', 'event', 'comments.user', 'reactions']);
        return response()->json(['data' => $question]);
    }

    public function update(UpdateQuestionRequest $request, Question $question)
    {
        // Check if user is the owner or an admin
        if ($question->user_id !== $request->user()->id && $request->user()->role !== \App\Enums\Role::ADMIN) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $question->update($request->validated());

        return response()->json(['data' => $question]);
    }

    public function destroy(Request $request, Question $question)
    {
        // Check if user is the owner or an admin
        if ($question->user_id !== $request->user()->id && $request->user()->role !== \App\Enums\Role::ADMIN) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $question->delete();
        return response()->json(null, 204);
    }
}
