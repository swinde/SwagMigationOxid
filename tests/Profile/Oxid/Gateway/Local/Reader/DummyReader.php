<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\MigrationOxid\Test\Profile\Oxid\Gateway\Local\Reader;

use Swag\MigrationOxid\Profile\Oxid\Gateway\Local\Reader\AbstractReader;
use SwagMigrationAssistant\Migration\MigrationContextInterface;

class DummyReader extends AbstractReader
{
    public function supports(MigrationContextInterface $migrationContext): bool
    {
        return true;
    }

    public function read(MigrationContextInterface $migrationContext): array
    {
        return [];
    }

    public function testUtf8ize(array $array): array
    {
        return $this->utf8ize($array);
    }
}
