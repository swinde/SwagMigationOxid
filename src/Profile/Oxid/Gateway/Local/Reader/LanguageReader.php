<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\MigrationOxid\Profile\Oxid\Gateway\Local\Reader;

use Doctrine\DBAL\Driver\ResultStatement;
use SwagMigrationAssistant\Migration\MigrationContextInterface;

abstract class LanguageReader extends AbstractReader
{
    public function read(MigrationContextInterface $migrationContext, array $params = []): array
    {
        $this->setConnection($migrationContext);

        return $this->fetchLocales();
    }

    protected function fetchLocales(): array
    {
        $query = $this->connection->createQueryBuilder();

        $query->from($this->tablePrefix . 'core_store', 'store');
        $query->leftJoin(
            'store',
            $this->tablePrefix . 'core_config_data',
            'localeconfig',
            'localeconfig.scope = \'stores\' AND localeconfig.path = \'general/locale/code\' AND store.store_id = localeconfig.scope_id'
        );
        $query->leftJoin(
            'store',
            $this->tablePrefix . 'core_config_data',
            'websitelocale',
            'websitelocale.scope = \'websites\' AND websitelocale.path = \'general/locale/code\' AND store.website_id = websitelocale.scope_id'
        );
        $query->innerJoin(
            'store',
            $this->tablePrefix . 'core_config_data',
            'defaultlocale',
            'defaultlocale.scope = \'default\' AND defaultlocale.path = \'general/locale/code\''
        );
        $query->addSelect('store.store_id');
        $query->addSelect('localeconfig.value as locale');
        $query->addSelect('websitelocale.value as websiteLocale');
        $query->addSelect('defaultlocale.value as defaultLocale');

        $query = $query->execute();
        if (!($query instanceof ResultStatement)) {
            return [];
        }

        $configurations = $query->fetchAll(\PDO::FETCH_ASSOC);
        $storeConfigs = [];
        $defaultAndWebsiteLocales = [];
        foreach ($configurations as $storeConfig) {
            if ($storeConfig['locale'] === null) {
                $storeConfig['locale'] = $storeConfig['defaultLocale'];
                if ($storeConfig['websiteLocale'] !== null) {
                    $storeConfig['locale'] = $storeConfig['websiteLocale'];
                }
            }
            if (isset($storeConfigs[$storeConfig['locale']])) {
                $storeConfigs[$storeConfig['locale']]['stores'][] = $storeConfig['store_id'];

                continue;
            }
            $storeConfigs[$storeConfig['locale']] = [
                'locale' => \str_replace('_', '-', $storeConfig['locale']),
                'stores' => [$storeConfig['store_id']],
            ];

            if ($storeConfig['websiteLocale'] !== null) {
                $defaultAndWebsiteLocales[$storeConfig['websiteLocale']] = $storeConfig['websiteLocale'];
            }

            if ($storeConfig['defaultLocale'] !== null) {
                $defaultAndWebsiteLocales[$storeConfig['defaultLocale']] = $storeConfig['defaultLocale'];
            }
        }

        foreach ($defaultAndWebsiteLocales as $locale) {
            if (!isset($storeConfigs[$locale])) {
                $storeConfigs[$locale] = [
                    'locale' => \str_replace('_', '-', $locale),
                    'stores' => [],
                ];
            }
        }

        return \array_values($storeConfigs);
    }
}
