<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\MigrationOxid\Profile\Oxid\Premapping;

use Shopware\Core\Framework\Context;
use Swag\MigrationOxid\Profile\Oxid\Gateway\OxidGatewayInterface;
use Swag\MigrationOxid\Profile\Oxid\OxidProfileInterface;
use SwagMigrationAssistant\Migration\Gateway\GatewayRegistryInterface;
use SwagMigrationAssistant\Migration\MigrationContextInterface;
use SwagMigrationAssistant\Migration\Premapping\AbstractPremappingReader;
use SwagMigrationAssistant\Migration\Premapping\PremappingChoiceStruct;
use SwagMigrationAssistant\Migration\Premapping\PremappingEntityStruct;
use SwagMigrationAssistant\Migration\Premapping\PremappingStruct;

abstract class CustomerGroupReader extends AbstractPremappingReader
{
    private const MAPPING_NAME = 'customer_group';

    /**
     * @var string[]
     */
    protected $preselectionDictionary = [];

    /**
     * @var GatewayRegistryInterface
     */
    private $gatewayRegistry;

    public function __construct(
        GatewayRegistryInterface $gatewayRegistry
    ) {
        $this->gatewayRegistry = $gatewayRegistry;
    }

    public static function getMappingName(): string
    {
        return self::MAPPING_NAME;
    }

    public function supports(MigrationContextInterface $migrationContext, array $entityGroupNames): bool
    {
        return $migrationContext->getProfile() instanceof OxidProfileInterface;
    }

    public function getPremapping(Context $context, MigrationContextInterface $migrationContext): PremappingStruct
    {
        $this->fillConnectionPremappingDictionary($migrationContext);
        $mapping = $this->getMapping();
        $choices = $this->getChoices($migrationContext);
        $this->setPreselection($mapping);

        return new PremappingStruct(self::getMappingName(), $mapping, $choices);
    }

    /**
     * @return PremappingEntityStruct[]
     */
    private function getMapping(): array
    {
        $OxidId = '';
        $entityData = [];
        if (isset($this->connectionPremappingDictionary['default_customer_group'])) {
            $OxidId = $this->connectionPremappingDictionary['default_customer_group']['destinationUuid'];
        }
        $entityData[] = new PremappingEntityStruct('default_customer_group', 'Default customer group', $OxidId);

        return $entityData;
    }

    /**
     * @return PremappingChoiceStruct[]
     */
    private function getChoices(MigrationContextInterface $migrationContext): array
    {
        /** @var OxidGatewayInterface $gateway */
        $gateway = $this->gatewayRegistry->getGateway($migrationContext);
        $customerGroups = $gateway->readCustomerGroups($migrationContext);

        $choices = [];
        foreach ($customerGroups as $customerGroup) {
            $choices[] = new PremappingChoiceStruct($customerGroup['customer_group_id'], $customerGroup['customer_group_code']);
        }

        return $choices;
    }

    /**
     * @param PremappingEntityStruct[] $mapping
     */
    private function setPreselection(array $mapping): void
    {
        foreach ($mapping as $item) {
            if ($item->getDestinationUuid() !== '' || !isset($this->preselectionDictionary[$item->getSourceId()])) {
                continue;
            }

            $item->setDestinationUuid($this->preselectionDictionary[$item->getSourceId()]);
        }
    }
}
