<?php

namespace App\Test\Util;

use App\Test\BaseTestCase;
use App\Util\ValidationContext;

/**
 * AppControllerTestCase
 *
 * @coversDefaultClass App\Util\ValidationContext
 * @group actual
 */
class ValidationContextTest extends BaseTestCase
{
    /**
     * Test instance
     *
     * @covers ::__construct
     * @covers ::getMessage
     * @return void
     */
    public function testInstance()
    {
        $instance = new ValidationContext();
        $this->assertInstanceOf(ValidationContext::class, $instance);
        $this->assertSame('Please check your data', $instance->getMessage());
    }

    /**
     * Test set message method.
     *
     * @covers ::setMessage
     * @return void
     */
    public function testSetMessage()
    {
        $defaultMessage = 'Default message';
        $notDefaultMessage = 'Not default message';
        $validationContext = new ValidationContext($defaultMessage);
        $this->assertSame($defaultMessage, $validationContext->getMessage());
        $validationContext->setMessage($notDefaultMessage);
        $this->assertSame($notDefaultMessage, $validationContext->getMessage());
    }

    /**
     * Test set error method
     *
     * @covers ::setError
     * @covers ::getErrors
     * @return void
     */
    public function testError()
    {
        $validationContext = new ValidationContext();
        $validationContext->setError('username', 'Username not valid');
        $validationContext->setError('password', 'Password not valid');
        $expected = [
            [
                'field' => 'username',
                'message' => 'Username not valid',
            ],
            [
                'field' => 'password',
                'message' => 'Password not valid',
            ],
        ];
        $this->assertSame($expected, $validationContext->getErrors());
    }

    /**
     * Test to array method
     *
     * @covers ::toArray
     * @covers ::setError
     * @return void
     */
    public function testToArray()
    {
        $validationContext = new ValidationContext();
        $validationContext->setError('username', 'Username not valid');
        $validationContext->setError('password', 'Password not valid');
        $expected = [
            "message" => 'Please check your data',
            "errors" => [
                [
                    'field' => 'username',
                    'message' => 'Username not valid',
                ],
                [
                    'field' => 'password',
                    'message' => 'Password not valid',
                ],
            ],
        ];
        $this->assertSame($expected, $validationContext->toArray());
    }

    /**
     * Test fails method
     *
     * @covers ::fails
     * @return void
     */
    public function testFails()
    {
        $validationContext = new ValidationContext();
        $this->assertFalse($validationContext->fails());
        $validationContext->setError('username', 'Username not valid');
        $this->assertTrue($validationContext->fails());
    }

    /**
     * Test if success is correct
     *
     * @covers ::success
     * @return void
     */
    public function testSuccess()
    {
        $validationContext = new ValidationContext();
        $this->assertTrue($validationContext->success());
        $validationContext->setError('username', 'Username not valid');
        $this->assertFalse($validationContext->success());
    }

    /**
     * Test clear
     *
     * @covers ::clear
     * @return void
     */
    public function testClear()
    {
        $validationContext = new ValidationContext();
        $validationContext->setError('username', 'Username not valid');
        $validationContext->clear();
        $this->assertNull($validationContext->getMessage());
        $this->assertEmpty($validationContext->getErrors());
    }
}
