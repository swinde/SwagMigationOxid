<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\MigrationOxid\Profile\Oxid\DataSelection\DataSet;

use Swag\MigrationOxid\Profile\Oxid\OxidProfileInterface;
use SwagMigrationAssistant\Migration\DataSelection\DataSet\DataSet;
use SwagMigrationAssistant\Migration\DataSelection\DefaultEntities;
use SwagMigrationAssistant\Migration\MigrationContextInterface;

class LanguageDataSet extends DataSet
{
    public static function getEntity(): string
    {
        return DefaultEntities::LANGUAGE;
    }

    public function supports(MigrationContextInterface $migrationContext): bool
    {
        return $migrationContext->getProfile() instanceof OxidProfileInterface;
    }
}
