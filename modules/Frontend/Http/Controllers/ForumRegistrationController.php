<?php

namespace Juzaweb\Frontend\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Juzaweb\Frontend\Http\Requests\ForumRegistrationRequest;
use Progmix\ForumRegistration\Models\ForumRegistration;

class ForumRegistrationController extends Controller
{
    public function show(): View
    {
        return view('frontend::register');
    }

    public function store(ForumRegistrationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $registration = ForumRegistration::create([
            'name' => $data['name'],
            'institution' => $data['institution'],
            'job_title' => $data['job_title'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'part_type' => $data['part_type'],
            'sponsor_type' => ($data['part_type'] === 'راعٍ') ? ($data['sponsor_type'] ?? null) : null,
        ]);

        $formEmail = get_config('email.contact_email');
        if ($formEmail) {
            $dynamicLink = config('app.url') . '/' . config('juzaweb.admin_prefix') . '/forum-registrations/' . $registration->id . '/edit';
            send_email_notification('Forum Registration', $formEmail, $dynamicLink);
        }

        return response()->json(['message' => 'Success'], 200);
    }
}
