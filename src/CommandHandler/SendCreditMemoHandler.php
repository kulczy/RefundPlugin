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

namespace Sylius\RefundPlugin\CommandHandler;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Command\SendCreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotFound;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface;
use Webmozart\Assert\Assert;

final class SendCreditMemoHandler
{
    public function __construct(
        private readonly RepositoryInterface $creditMemoRepository,
        private readonly CreditMemoEmailSenderInterface $creditMemoEmailSender,
    ) {
    }

    public function __invoke(SendCreditMemo $command): void
    {
        $creditMemoNumber = $command->number();
        Assert::notNull($creditMemoNumber);

        /** @var CreditMemoInterface|null $creditMemo */
        $creditMemo = $this->creditMemoRepository->findOneBy(['number' => $creditMemoNumber]);
        if ($creditMemo === null) {
            throw CreditMemoNotFound::withNumber($creditMemoNumber);
        }

        /** @var OrderInterface|null $order */
        $order = $creditMemo->getOrder();
        Assert::notNull($order);

        /** @var CustomerInterface|null $customer */
        $customer = $order->getCustomer();
        Assert::notNull($customer, 'Credit memo order has no customer');

        /** @var string|null $recipient */
        $recipient = $customer->getEmail();
        Assert::notNull($recipient);

        $this->creditMemoEmailSender->send($creditMemo, $recipient);
    }
}
