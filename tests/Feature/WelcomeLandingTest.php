<?php

it('renders the CRM landing at /', function () {
    config()->set('saas.enabled', false);
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Turn every conversation into your next opportunity.', false);
    $response->assertSee('Get started free', false);
    $response->assertSee('Liberu CRM', false);
    $response->assertSee('href="#features"', false);
});

it('links the landing CTAs to the real auth routes', function () {
    $this->get('/')
        ->assertSee(route('register'), false)
        ->assertSee(route('login'), false);
});

it('no longer ships the stock Laravel welcome scaffold', function () {
    $this->get('/')->assertDontSee("Let's get started", false);
});
