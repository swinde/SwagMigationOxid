<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\MigrationOxid\Profile\Oxid\DataSelection;

use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\CategoryDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\CountryDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\CurrencyDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\CustomerGroupDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\LanguageDataSet;
use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\SalesChannelDataSet;
use Swag\MigrationOxid\Profile\Oxid\OxidProfileInterface;
use SwagMigrationAssistant\Migration\DataSelection\DataSelectionInterface;
use SwagMigrationAssistant\Migration\DataSelection\DataSelectionStruct;
use SwagMigrationAssistant\Migration\MigrationContextInterface;

class BasicSettingsDataSelection implements DataSelectionInterface
{
    public const IDENTIFIER = 'basicSettings';

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
            'swag-migration.index.selectDataCard.dataSelection.basicSettings',
            -100,
            true,
            DataSelectionStruct::BASIC_DATA_TYPE,
            true
        );
    }

    public function getDataSets(): array
    {
        return [
            new LanguageDataSet(),
            new CustomerGroupDataSet(),
            new CategoryDataSet(),
            new CountryDataSet(),
            new CurrencyDataSet(),
            new SalesChannelDataSet(),
        ];
    }

    public function getDataSetsRequiredForCount(): array
    {
        return $this->getDataSets();
    }
}
