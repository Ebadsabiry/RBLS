<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteTest extends TestCase
{
    public function test_all_pages_render_with_shared_navigation_and_assets(): void
    {
        foreach (['/', '/about', '/services', '/projects', '/company-profile', '/contact'] as $path) {
            $this->get($path)->assertOk()->assertSee('css/site.css')->assertSee('js/site.js')->assertSee('Skip to content')->assertSee('info@romanbahram.com');
        }
    }

    public function test_portfolio_contains_all_fifty_three_records_and_filters(): void
    {
        $response = $this->get('/projects');
        $response->assertOk()->assertSee('project-search')->assertSee('project-client')->assertSee('JOYA')->assertSee('Norwegian Afghanistan Committee');
        $this->assertSame(53, substr_count($response->getContent(), 'data-project data-client='));
    }

    public function test_inquiry_is_validated_and_saved_privately_without_sending_log_mail(): void
    {
        Storage::fake('local');
        Mail::fake();
        config(['mail.default' => 'log']);
        $this->post('/contact', $this->validInquiry())->assertRedirect('/contact')->assertSessionHas('inquiry_status');
        $files = Storage::disk('local')->files('inquiries');
        $this->assertCount(1, $files);
        $saved = json_decode(Storage::disk('local')->get($files[0]), true);
        $this->assertSame('partner@example.com', $saved['email']);
        $this->assertSame('Procurement', $saved['service']);
        $this->assertArrayHasKey('reference', $saved);
        Mail::assertNothingSent();
    }

    public function test_invalid_and_bot_inquiries_are_rejected_without_storage(): void
    {
        Storage::fake('local');
        $this->post('/contact', [])->assertSessionHasErrors(['name', 'email', 'service', 'message']);
        $data = $this->validInquiry();
        $data['website'] = 'spam';
        $this->post('/contact', $data)->assertSessionHasErrors('website');
        $data = $this->validInquiry();
        $data['service'] = 'Invalid service';
        $this->post('/contact', $data)->assertSessionHasErrors('service');
        $this->assertCount(0, Storage::disk('local')->files('inquiries'));
    }

    public function test_valid_inquiry_can_notify_with_configured_mail_transport(): void
    {
        Storage::fake('local');
        Mail::shouldReceive('raw')->once()->andReturnNull();
        config(['mail.default' => 'smtp']);
        $this->post('/contact', $this->validInquiry())->assertRedirect('/contact');
        $this->assertCount(1, Storage::disk('local')->files('inquiries'));
    }

    private function validInquiry(): array
    {
        return ['name' => 'Test Partner', 'organization' => 'Example Organization', 'email' => 'partner@example.com', 'service' => 'Procurement', 'message' => 'Please source and deliver office supplies for our Kabul project.'];
    }
}
