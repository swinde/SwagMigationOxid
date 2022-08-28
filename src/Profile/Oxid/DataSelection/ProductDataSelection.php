<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\MigrationOxid\Profile\Oxid\DataSelection;

use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\CrossSellingDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\ManufacturerDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\ProductChildMultiSelectPropertyRelationDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\ProductChildMultiSelectTextPropertyRelationDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\ProductChildPropertyRelationDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\ProductCustomFieldDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\ProductDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\ProductMultiSelectPropertyRelationDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\ProductMultiSelectTextPropertyRelationDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\ProductOptionRelationDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\ProductPropertyRelationDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\PropertyGroupDataSet;
use Swag\MigrationOxid\Profile\Oxid\OxidProfileInterface;
use SwagMigrationAssistant\Migration\DataSelection\DataSelectionInterface;
use SwagMigrationAssistant\Migration\DataSelection\DataSelectionStruct;
use SwagMigrationAssistant\Migration\MigrationContextInterface;

class ProductDataSelection implements DataSelectionInterface
{
    public const IDENTIFIER = 'products';

    public function supports(MigrationContextInterface $migrationContext): bool
    {
        return $migrationContext->getProfile() instanceof OxidProfileInterface;
    }

    public function getData(): DataSelectionStruct
    {
        return new DataSelectionStruct(
            self::IDENTIFIER,
            $this->getDataSets(),
            $this->getDataSetsRequiredForCount(),
            'swag-migration.index.selectDataCard.dataSelection.products',
            100,
            true
        );
    }

    public function getDataSets(): array
    {
        return [
            new ManufacturerDataSet(),
            new PropertyGroupDataSet(),
            new ProductCustomFieldDataSet(),
            new ProductDataSet(),
            new ProductPropertyRelationDataSet(),
            new ProductChildPropertyRelationDataSet(),
            new ProductMultiSelectPropertyRelationDataSet(),
            new ProductMultiSelectTextPropertyRelationDataSet(),
            new ProductChildMultiSelectPropertyRelationDataSet(),
            new ProductChildMultiSelectTextPropertyRelationDataSet(),
            new ProductOptionRelationDataSet(),
            new CrossSellingDataSet(),
        ];
    }

    public function getDataSetsRequiredForCount(): array
    {
        return [
            new ProductDataSet(),
        ];
    }
}
