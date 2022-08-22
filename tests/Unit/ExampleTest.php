<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_true_is_true()
    {
        $this->assertTrue(true);
    }

    /**
     * A basic test example.
     * @test
     *
     * @return void
     */
    public function that_true_is_true()
    {
        $this->assertTrue(true);
    }
}
