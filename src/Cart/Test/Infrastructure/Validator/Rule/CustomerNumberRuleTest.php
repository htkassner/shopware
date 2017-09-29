<?php declare(strict_types=1);
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Cart\Test\Infrastructure\Validator\Rule;

use PHPUnit\Framework\TestCase;
use Shopware\Cart\Cart\CalculatedCart;
use Shopware\CartBridge\Rule\CustomerNumberRule;
use Shopware\Context\Struct\ShopContext;
use Shopware\Customer\Struct\CustomerBasicStruct;
use Shopware\Framework\Struct\StructCollection;

class CustomerNumberRuleTest extends TestCase
{
    public function testExactMatch(): void
    {
        $rule = new CustomerNumberRule(['NO. 1']);

        $cart = $this->createMock(CalculatedCart::class);

        $customer = new CustomerBasicStruct();
        $customer->setNumber('NO. 1');

        $context = $this->createMock(ShopContext::class);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertTrue(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testMultipleNumbers(): void
    {
        $rule = new CustomerNumberRule(['NO. 1', 'NO. 2', 'NO. 3']);

        $cart = $this->createMock(CalculatedCart::class);

        $customer = new CustomerBasicStruct();
        $customer->setNumber('NO. 2');

        $context = $this->createMock(ShopContext::class);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertTrue(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testCaseInsensitive(): void
    {
        $rule = new CustomerNumberRule(['NO. 1']);

        $cart = $this->createMock(CalculatedCart::class);

        $customer = new CustomerBasicStruct();
        $customer->setNumber('no. 1');

        $context = $this->createMock(ShopContext::class);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertTrue(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testWithoutCustomer(): void
    {
        $rule = new CustomerNumberRule(['NO. 1']);

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue(null));

        $this->assertFalse(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testNotMatch(): void
    {
        $rule = new CustomerNumberRule(['NO. 1']);

        $cart = $this->createMock(CalculatedCart::class);

        $customer = new CustomerBasicStruct();
        $customer->setNumber('no. 2');

        $context = $this->createMock(ShopContext::class);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertFalse(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }
}