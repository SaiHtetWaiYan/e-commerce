<?php

it('shows the terms of service page', function () {
    $this->get(route('storefront.pages.terms'))
        ->assertSuccessful()
        ->assertSee('Terms of Service');
});

it('shows the privacy policy page', function () {
    $this->get(route('storefront.pages.privacy'))
        ->assertSuccessful()
        ->assertSee('Privacy Policy');
});
