<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_user_role_helper_methods(): void
    {
        $admin = new User(['role' => User::ROLE_ADMIN]);
        $operator = new User(['role' => User::ROLE_OPERATOR]);
        $customer = new User(['role' => User::ROLE_CUSTOMER]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isOperator());
        $this->assertFalse($admin->isCustomer());

        $this->assertTrue($operator->isOperator());
        $this->assertFalse($operator->isAdmin());
        $this->assertFalse($operator->isCustomer());

        $this->assertTrue($customer->isCustomer());
        $this->assertFalse($customer->isAdmin());
        $this->assertFalse($customer->isOperator());

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->hasRole(['admin', 'operator']));
        $this->assertFalse($customer->hasRole(['admin', 'operator']));
    }
}
