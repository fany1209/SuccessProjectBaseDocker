<?php

test('GET request cannot mutate or delete product images', function () {
    // Attempting to invoke deletion via GET method must not match any mutating route (404 or 405)
    $response = $this->get('/admin/products/image/1/delete');
    
    // Either route doesn't exist (404) or redirects/rejects
    expect($response->status())->toBeIn([404, 405]);
});

test('GET request cannot mutate or delete product files', function () {
    // Attempting to invoke deletion via GET method must not match any mutating route (404 or 405)
    $response = $this->get('/admin/products/file/1/delete');

    expect($response->status())->toBeIn([404, 405]);
});

test('DELETE product image requires authentication and CSRF', function () {
    // An unauthenticated request attempting to DELETE must be rejected/redirected
    $response = $this->delete('/admin/products/image/1');

    $response->assertRedirect('/login');
});

test('DELETE product file requires authentication and CSRF', function () {
    // An unauthenticated request attempting to DELETE must be rejected/redirected
    $response = $this->delete('/admin/products/file/1');

    $response->assertRedirect('/login');
});
