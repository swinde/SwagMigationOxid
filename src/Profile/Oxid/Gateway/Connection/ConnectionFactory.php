<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\MigrationOxid\Profile\Oxid\Gateway\Connection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Swag\MigrationOxid\Exception\InvalidTablePrefixException;
use SwagMigrationAssistant\Migration\MigrationContextInterface;

class ConnectionFactory implements ConnectionFactoryInterface
{
    public function createDatabaseConnection(MigrationContextInterface $migrationContext): ?Connection
    {
        $connection = $migrationContext->getConnection();

        if ($connection === null) {
            return null;
        }

        $credentials = $connection->getCredentialFields();

        if ($credentials === null) {
            return null;
        }

        $connectionParams = [
            'dbname' => $credentials['dbName'] ?? '',
            'user' => $credentials['dbUser'] ?? '',
            'password' => $credentials['dbPassword'] ?? '',
            'host' => $credentials['dbHost'] ?? '',
            'port' => $credentials['dbPort'] ?? '',
            'driver' => 'pdo_mysql',
            'charset' => 'utf8mb4',
        ];

        $connection = DriverManager::getConnection($connectionParams);

        if (!isset($credentials['tablePrefix']) || $credentials['tablePrefix'] === '') {
            return $connection;
        }
        $schemaManager = $connection->getSchemaManager();
        if (!$schemaManager->tablesExist([$credentials['tablePrefix'] . 'customer_entity'])) {
            throw new InvalidTablePrefixException('The configured table prefix is invalid.');
        }

        return $connection;
    }
}
