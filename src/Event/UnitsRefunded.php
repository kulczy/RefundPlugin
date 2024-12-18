<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Webmozart\Assert\Assert;

class UnitsRefunded
{
    /** @var array|ShipmentRefund[] */
    private array $shipments = [];

    /**
     * @param array<UnitRefundInterface[]|OrderItemUnitRefund[]> $units
     * @param int|array<int, int> $paymentMethodId
     */
    public function __construct(
        private readonly string $orderNumber,
        /** @var array|UnitRefundInterface[]|OrderItemUnitRefund[] */
        private readonly array $units,
        private int|array $paymentMethodId,
        private int $amount,
        private string|int $currencyCode,
        private string $comment,
    ) {
        $args = func_get_args();

        if (is_array($paymentMethodId)) {
            if (!isset($args[6])) {
                throw new \InvalidArgumentException('The 7th argument must be present.');
            }

            /** @phpstan-ignore-next-line */
            $this->shipments = $paymentMethodId;
            $this->paymentMethodId = $amount;
            /** @phpstan-ignore-next-line */
            $this->amount = $currencyCode;
            $this->currencyCode = $comment;
            $this->comment = $args[6];

            trigger_deprecation('sylius/refund-plugin', '1.4', sprintf('Passing an array as a 3th argument of "%s" constructor is deprecated and will be removed in 2.0.', self::class));
        }

        Assert::allIsInstanceOf($units, UnitRefundInterface::class);
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    /** @return array|UnitRefundInterface[] */
    public function units(): array
    {
        return $this->units;
    }

    /**
     * @deprecated since 1.4, to be removed in 2.0. Use "units" method instead.
     *
     * @return array|ShipmentRefund[]
     */
    public function shipments(): array
    {
        trigger_deprecation('sylius/refund-plugin', '1.4', sprintf('The "%s::shipments" method is deprecated and will be removed in 2.0.', self::class));

        return $this->shipments;
    }

    public function paymentMethodId(): int
    {
        Assert::integer($this->paymentMethodId);

        return $this->paymentMethodId;
    }

    public function amount(): int
    {
        Assert::integer($this->amount);

        return $this->amount;
    }

    public function currencyCode(): string
    {
        Assert::string($this->currencyCode);

        return $this->currencyCode;
    }

    public function comment(): string
    {
        return $this->comment;
    }
}
