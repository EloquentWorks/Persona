<?php

namespace EloquentWorks\Persona\Http\Controllers;

use EloquentWorks\Persona\Events\PersonaViewed;
use EloquentWorks\Persona\Models\Persona;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;
use LogicException;

/**
 * Controller responsible for handling public Persona profile views.
 */
class PersonaController extends Controller
{
    /**
     * Display a public Persona profile.
     *
     * @param  string  $persona  The profile slug from the route.
     * @return View Returns the rendered profile page.
     */
    public function show(string $persona): View
    {
        // Resolve the Persona profile based on the provided slug.
        $profile = $this->resolveProfile($persona);
    
        // Record a view for the profile if profile views are enabled in the configuration.
        if (config('persona.features.profile_views', true)) {
            $profile->recordView();
        }
    
        // Dispatch a PersonaViewed event if event dispatching is enabled in the configuration.
        if (config('persona.dispatch_events', true)) {
            event(new PersonaViewed($profile));
        }
    
        // Determine the view to use for rendering the profile, falling back to a default if not configured.
        $view = config('persona.views.show', 'persona::show');
    
        // Render the profile view with the resolved profile and associated user data.
        return view(is_string($view) && $view !== '' ? $view : 'persona::show', [
            'persona' => $profile,
            'profile' => $profile,
            'user' => $profile->user,
        ]);
    }

    /**
     * Resolve a visible profile by slug.
     *
     * @param  string  $slug  The public profile slug.
     * @return Persona Returns the resolved Persona profile.
     */
    protected function resolveProfile(string $slug): Persona
    {
        // Retrieve the configured Persona model class from the configuration, defaulting to the Persona model if not set.
        $personaModel = config('persona.models.persona', Persona::class);

        // Validate that the resolved model is a string and is a subclass of the Eloquent Model class.
        if (! is_string($personaModel) || ! is_subclass_of($personaModel, Model::class)) {
            throw new LogicException('Unable to resolve the configured Persona model.');
        }

        /** @var class-string<Persona> $personaModel */
        $personaModel = $personaModel;

        // Query the Persona model for a visible profile matching the provided slug, throwing a 404 error if not found.
        return $personaModel::visible()->where('slug', $slug)->firstOrFail();
    }
}
