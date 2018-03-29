<?php

namespace App\Test;

/**
 * util.
 */
class ServicesTestCase extends BaseTestCase
{
    /**
     * Function is_email.
     */
    public function testIsEmail()
    {
        $this->assertTrue(is_email('test@example.com'));
        $this->assertFalse(is_email('test'));
        $this->assertFalse(is_email('test@example'));
        $this->assertFalse(is_email('example.com'));
    }
}
