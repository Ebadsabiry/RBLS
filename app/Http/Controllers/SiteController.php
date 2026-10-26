<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SiteController extends Controller
{
    private const SERVICES = ['Procurement', 'Transportation', 'Vehicle rental', 'Warehousing', 'Distribution', 'Office and IT supplies', 'Humanitarian supplies', 'Construction supplies', 'Fuel and automotive', 'Customized logistics'];

    public function home(): View { return view('pages.home'); }
    public function about(): View { return view('pages.about'); }
    public function services(): View { return view('pages.services'); }

    public function projects(): View
    {
        $projects = json_decode(file_get_contents(resource_path('data/projects.json')), true, 512, JSON_THROW_ON_ERROR);
        foreach ($projects as &$project) {
            $service = strtolower($project['service']);
            $project['category'] = str_contains($service, 'printing') ? 'printing' : (str_contains($service, 'construction') ? 'construction' : (str_contains($service, 'transportation') ? 'transport' : 'procurement'));
            preg_match('/\(([^)]+)\)/', $project['client'], $match);
            $project['short_client'] = $match[1] ?? $project['client'];
        }
        unset($project);
        $clients = array_values(array_unique(array_column($projects, 'client')));
        sort($clients);
        return view('pages.projects', compact('projects', 'clients'));
    }

    public function contact(): View
    {
        return view('pages.contact', ['services' => self::SERVICES]);
    }

    public function profile(): View
    {
        $profileSize = number_format(filesize(public_path('RBLS Data/RBLS Profile 2026.pdf')) / 1048576, 1).' MB';
        return view('pages.profile', compact('profileSize'));
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:254'],
            'phone' => ['nullable', 'string', 'max:40'],
            'service' => ['required', Rule::in(self::SERVICES)],
            'destination' => ['nullable', 'string', 'max:160'],
            'timeline' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'website' => ['nullable', 'prohibited'],
        ]);
        unset($data['website']);
        $reference = (string) Str::uuid();
        $data['reference'] = $reference;
        $data['submitted_at'] = now()->toIso8601String();

        if (config('inquiries.delivery') === 'mail') {
            // Never acknowledge an inquiry that exists only in ephemeral storage.
            try {
                if (! $this->hasDeliveryTransport()) {
                    throw new \RuntimeException('Inquiry mail transport is not configured.');
                }
                $this->notifyInquiry($data, $reference);
            } catch (\Throwable $exception) {
                Log::error('Inquiry delivery failed.', ['reference' => $reference]);
                return back()->withErrors(['submission' => 'We could not send your inquiry. Please email info@romanbahram.com or call +93 79 25 26 410.']);
            }

            return redirect()->route('contact')->with('inquiry_status', 'Your inquiry has been sent to our team. Reference: '.substr($reference, 0, 8).'. For urgent requirements, please call +93 79 25 26 410 or email info@romanbahram.com.');
        }

        try {
            $saved = Storage::disk('local')->put('inquiries/'.$reference.'.json', json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
            if (!$saved) {
                throw new \RuntimeException('Unable to persist inquiry.');
            }
        } catch (\Throwable $exception) {
            Log::error('Inquiry storage failed.', ['reference' => $reference]);
            return back()->withInput()->withErrors(['submission' => 'We could not save your inquiry. Please try again or email info@romanbahram.com.']);
        }

        // Local logging is not email delivery. Only notify with a configured delivery transport.
        if ($this->hasDeliveryTransport()) {
            try {
                $this->notifyInquiry($data, $reference);
            } catch (\Throwable $exception) {
                Log::warning('Inquiry saved; notification delivery failed.', ['reference' => $reference]);
            }
        }

        return redirect()->route('contact')->with('inquiry_status', 'Your inquiry has been recorded. Reference: '.substr($reference, 0, 8).'. For urgent requirements, please call +93 79 25 26 410 or email info@romanbahram.com.');
    }

    private function hasDeliveryTransport(): bool
    {
        $mailer = config('mail.default');
        return in_array(config("mail.mailers.{$mailer}.transport"), ['smtp', 'sendmail', 'ses', 'ses-v2', 'postmark', 'resend', 'mailgun'], true);
    }

    private function notifyInquiry(array $data, string $reference): void
    {
        $body = "New RBLS website inquiry\nReference: {$reference}\n\n";
        foreach ($data as $key => $value) {
            $body .= ucfirst(str_replace('_', ' ', $key)).': '.($value ?? 'Not provided')."\n";
        }
        Mail::raw($body, function ($message) use ($data, $reference) {
            $message->to(config('inquiries.recipient'))->replyTo($data['email'])->subject('RBLS inquiry: '.substr($reference, 0, 8));
        });
    }
}
