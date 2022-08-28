<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\MigrationOxid\Migration\Mapping;

use Shopware\Core\Framework\Context;
use SwagMigrationAssistant\Migration\Mapping\MappingServiceInterface;

interface OxidMappingServiceInterface extends MappingServiceInterface
{
    public function getOxidCountryUuid(string $iso, string $connectionId, Context $context): ?string;

    public function getTransactionStateUuid(string $state, Context $context): ?string;

    public function getTaxRate(string $uuid, Context $context): ?float;
}
