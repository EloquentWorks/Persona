<?php

namespace EloquentWorks\Persona\Http\Controllers;

use EloquentWorks\Persona\Events\PersonaViewed;
use EloquentWorks\Persona\Models\Persona;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LogicException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller responsible for handling public Persona profile views.
 */
class PersonaController extends Controller
{
    /**
     * Display a public Persona profile.
     *
     * @param  string  $persona  The profile slug from the route.
     * @param  Request  $request  The incoming HTTP request.
     * @return View Returns the rendered profile page.
     */
    public function show(string $persona, Request $request): View
    {
        // Resolve the Persona profile by slug without applying the visible
        // scope yet. This lets Persona distinguish a private profile from a
        // profile that does not exist when configured to render a private page.
        $profile = $this->resolveProfile($persona);

        // Determine if the current authenticated user is the owner of the profile and if the profile is visible.
        $isOwner = $this->isOwner($profile, $request);
        $isVisible = $profile->isVisible();

        // Determine if owners are allowed to view private profiles based on the configuration.
        $ownerCanViewPrivate = (bool) config(
            'persona.visibility.owner_can_view_private',
            true
        );

        // If the profile is not visible, check if the current user is the owner
        // and if owners are allowed to view private profiles.
        if (! $isVisible) {
            if (! $isOwner || ! $ownerCanViewPrivate) {
                return $this->privateProfileResponse();
            }
        }

        // Private-profile placeholder responses never increment views.
        if ($isVisible && config('persona.features.profile_views', true)) {
            $profile->recordView();
        }

        // Private-profile placeholder responses never dispatch PersonaViewed.
        if ($isVisible && config('persona.dispatch_events', true)) {
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
     * Resolve a profile by slug.
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

        // Resolve by slug first. Visibility is handled by show() so configured
        // private-profile responses can distinguish private from missing.
        return $personaModel::query()->where('slug', $slug)->firstOrFail();
    }

    /**
     * Determine whether the current authenticated user owns the profile.
     *
     * @param  Persona  $profile  The Persona profile to check ownership against.
     * @param  Request  $request  The incoming HTTP request.
     * @return bool Returns true if the authenticated user owns the profile, false otherwise.
     */
    protected function isOwner(Persona $profile, Request $request): bool
    {
        // Determine the currently authenticated user from the request.
        $viewer = $request->user();

        // Validate that the viewer is an instance of the Eloquent Model class. If not, return false.
        if (! $viewer instanceof Model) {
            return false;
        }

        // Retrieve the owner of the profile, which is the associated user model.
        $owner = $profile->user;

        // Check if the owner is an instance of the Eloquent Model class and if the owner is the same as the viewer.
        return $owner instanceof Model && $owner->is($viewer);
    }

    /**
     * Render the configured response for a private profile.
     *
     * The private view intentionally receives no Persona model and no user
     * model so a customized view cannot accidentally leak private fields.
     *
     * @return View Returns the rendered private profile response.
     */
    protected function privateProfileResponse(): View
    {
        // Determine the configured response for private profiles. If the response is '404',
        // throw a NotFoundHttpException. If the response is not 'view', throw a LogicException
        // indicating an invalid configuration value.
        $response = config('persona.visibility.private_profile_response', '404');

        // If the response is configured to return a 404, throw a NotFoundHttpException
        // to indicate that the profile was not found.
        if ($response === '404') {
            throw new NotFoundHttpException;
        }

        // If the response is not 'view', throw a LogicException indicating an invalid configuration value.
        if ($response !== 'view') {
            throw new LogicException(
                'Invalid persona.visibility.private_profile_response value. Supported values are [404, view].'
            );
        }

        // Determine the view to use for rendering the private profile response, falling back to a default if not configured.
        $view = config('persona.views.private', 'persona::private');

        // Render the private profile view with a flag indicating that the profile is private.
        // The view intentionally receives no Persona model and no user model to prevent accidental leakage of private fields.
        return view(
            is_string($view) && $view !== '' ? $view : 'persona::private',
            [
                'isPrivate' => true,
            ],
        );
    }
}
