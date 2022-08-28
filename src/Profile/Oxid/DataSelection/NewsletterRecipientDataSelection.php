<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\MigrationOxid\Profile\Oxid\DataSelection;

use Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet\NewsletterRecipientDataSet;
use Swag\MigrationOxid\Profile\Oxid\OxidProfileInterface;
use SwagMigrationAssistant\Migration\DataSelection\DataSelectionInterface;
use SwagMigrationAssistant\Migration\DataSelection\DataSelectionStruct;
use SwagMigrationAssistant\Migration\MigrationContextInterface;

class NewsletterRecipientDataSelection implements DataSelectionInterface
{
    public const IDENTIFIER = 'newsletterRecipient';

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
            'swag-migration.index.selectDataCard.dataSelection.newsletterRecipient',
            400,
            false
        );
    }

    public function getDataSets(): array
    {
        return [
            new NewsletterRecipientDataSet(),
        ];
    }

    public function getDataSetsRequiredForCount(): array
    {
        return $this->getDataSets();
    }
}
