<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\MigrationOxid\Test\Profile\Oxid\Premapping;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Shipping\ShippingMethodDefinition;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Swag\MigrationOxid\Profile\Oxid19\Gateway\Local\Oxid19LocalGateway;
use Swag\MigrationOxid\Profile\Oxid19\Oxid19Profile;
use Swag\MigrationOxid\Profile\Oxid19\Premapping\Oxid19ShippingMethodReader;
use SwagMigrationAssistant\Migration\Connection\SwagMigrationConnectionEntity;
use SwagMigrationAssistant\Migration\Gateway\GatewayRegistry;
use SwagMigrationAssistant\Migration\MigrationContext;
use SwagMigrationAssistant\Migration\MigrationContextInterface;
use SwagMigrationAssistant\Migration\Premapping\PremappingStruct;
use SwagMigrationAssistant\Profile\Shopware\Gateway\Local\ShopwareLocalGateway;

class ShippingMethodReaderTest extends TestCase
{
    use KernelTestBehaviour;

    /**
     * @var MigrationContextInterface
     */
    private $migrationContext;

    /**
     * @var Oxid19ShippingMethodReader
     */
    private $reader;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var ShippingMethodEntity
     */
    private $dhlMock;

    /**
     * @var ShippingMethodEntity
     */
    private $upsMock;

    public function setUp(): void
    {
        $this->context = Context::createDefaultContext();

        $connection = new SwagMigrationConnectionEntity();
        $connection->setId(Uuid::randomHex());
        $connection->setProfileName(Oxid19Profile::PROFILE_NAME);
        $connection->setGatewayName(ShopwareLocalGateway::GATEWAY_NAME);
        $connection->setCredentialFields([]);

        $this->dhlMock = new ShippingMethodEntity();
        $this->dhlMock->setId(Uuid::randomHex());
        $this->dhlMock->setName('DHL');

        $this->upsMock = new ShippingMethodEntity();
        $this->upsMock->setId(Uuid::randomHex());
        $this->upsMock->setName('UPS');

        $premapping = [[
            'entity' => 'shipping_method',
            'mapping' => [
                0 => [
                    'sourceId' => 'dhl',
                    'description' => 'dhl',
                    'destinationUuid' => $this->dhlMock->getId(),
                ],
                1 => [
                    'sourceId' => 'ups',
                    'description' => 'ups',
                    'destinationUuid' => $this->upsMock->getId(),
                ],

                2 => [
                    'sourceId' => 'shipment-invalid',
                    'description' => 'shipment-invalid',
                    'destinationUuid' => Uuid::randomHex(),
                ],
            ],
        ]];
        $connection->setPremapping($premapping);

        $mock = $this->createMock(EntityRepository::class);
        $mock->method('search')->willReturn(new EntitySearchResult(ShippingMethodDefinition::ENTITY_NAME, 2, new EntityCollection([$this->dhlMock, $this->upsMock]), null, new Criteria(), $this->context));

        $gatewayMock = $this->createMock(Oxid19LocalGateway::class);
        $gatewayMock->method('readCarriers')->willReturn([
            ['carrier_id' => 'ups', 'value' => 'UPS-Shipment'],
            ['carrier_id' => 'dhl', 'value' => 'DHL-Shipment'],
            ['carrier_id' => 'withoutDescription'],
            ['carrier_id' => 'shipment-invalid', 'value' => 'shipment-invalid'],
        ]);

        $gatewayRegistryMock = $this->createMock(GatewayRegistry::class);
        $gatewayRegistryMock->method('getGateway')->willReturn($gatewayMock);

        $this->migrationContext = new MigrationContext(
            new Oxid19Profile(),
            $connection
        );

        $this->reader = new Oxid19ShippingMethodReader($gatewayRegistryMock, $mock);
    }

    public function testGetPremapping(): void
    {
        $result = $this->reader->getPremapping($this->context, $this->migrationContext);

        static::assertInstanceOf(PremappingStruct::class, $result);

        static::assertCount(5, $result->getMapping());
        static::assertCount(2, $result->getChoices());

        $choices = $result->getChoices();
        static::assertSame('DHL', $choices[0]->getDescription());
        static::assertSame('UPS', $choices[1]->getDescription());

        $mapping = $result->getMapping();
        static::assertSame('DHL-Shipment', $mapping[0]->getDescription());
        static::assertSame('Standard shipping method', $mapping[1]->getDescription());
        static::assertSame('UPS-Shipment', $mapping[2]->getDescription());
        static::assertSame('shipment-invalid', $mapping[3]->getDescription());
        static::assertSame('withoutDescription', $mapping[4]->getDescription());
        static::assertSame($this->dhlMock->getId(), $result->getMapping()[0]->getDestinationUuid());
        static::assertEmpty($result->getMapping()[1]->getDestinationUuid());
        static::assertSame($this->upsMock->getId(), $result->getMapping()[2]->getDestinationUuid());
        static::assertEmpty($result->getMapping()[3]->getDestinationUuid());
        static::assertEmpty($result->getMapping()[4]->getDestinationUuid());
    }
}
