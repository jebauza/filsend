<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /* public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    } */

    public function testBasicTest()
    {
        $col1 = collect([1,3,6]);
        $col2 = collect([2,6,8]);

        dd($col1->merge($col2));
    }
}
