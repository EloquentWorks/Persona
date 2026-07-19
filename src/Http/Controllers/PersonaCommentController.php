<?php

namespace EloquentWorks\Persona\Http\Controllers;

use EloquentWorks\Persona\Events\PersonaCommentCreated;
use EloquentWorks\Persona\Models\Persona;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Controller for handling persona comments.
 */
final class PersonaCommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Persona $persona): RedirectResponse
    {
        // Check if comments are enabled in the configuration
        abort_unless((bool) config('persona.comments.enabled', true), 404);

        // Check if guest comments are allowed in the configuration
        if (! (bool) config('persona.comments.allow_guest_comments', false)) {
            abort_unless($request->user() !== null, 403);
        }

        // Validate the incoming request data for the comment
        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:'.((int) config('persona.comments.max_length', 1000))],
            'parent_id' => ['nullable', 'integer'],
        ]);

        // Create a new comment associated with the persona
        $comment = $persona->comments()->create([
            'body' => $validated['body'],
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => $request->user()?->getAuthIdentifier(),
            'is_approved' => (bool) config('persona.comments.auto_approve', true),
        ]);

        // Fire an event indicating that a new comment has been created
        event(new PersonaCommentCreated($persona, $comment));

        // Return back to the previous page with a success message
        return back()->with((string) config('persona.flash.success_key', 'status'), (string) config('persona.messages.comment_created', 'Comment posted.'));
    }
}
