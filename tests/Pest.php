<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

/*
  Test Case
*/

uses(
    Tests\TestCase::class,
    RefreshDatabase::class,
)->in('Feature');

/*
  Expectations
*/

// Custom expectation: assert a response is a valid product resource
expect()->extend('toBeAProduct', function () {
    return $this->toHaveKeys(['id', 'name', 'price', 'stock', 'in_stock', 'created_at', 'updated_at']);
});
