<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PackageVersions\Versions;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\StaticKernelPluginLoader;
use Shopware\Development\Kernel;
use Swag\MigrationOxid\SwagMigrationOxid;
use SwagMigrationAssistant\SwagMigrationAssistant;
use Symfony\Component\Dotenv\Dotenv;

$classLoader = require __DIR__ . '/../../../../vendor/autoload.php';
(new Dotenv(true))->load(__DIR__ . '/../../../../.env');

$shopwareVersion = Versions::getVersion('shopware/platform');

$oxidPluginRootPath = \dirname(__DIR__);
$oxidComposerJson = \json_decode((string) \file_get_contents($oxidPluginRootPath . '/composer.json'), true);
$assistantPluginRootPath = $oxidPluginRootPath . '/../SwagMigrationAssistant';
$assistantComposerJson = \json_decode((string) \file_get_contents($assistantPluginRootPath . '/composer.json'), true);

$swagMigrationOxid = [
    'autoload' => $oxidComposerJson['autoload'],
    'baseClass' => SwagMigrationOxid::class,
    'managedByComposer' => false,
    'active' => true,
    'path' => $oxidPluginRootPath,
    'name' => 'SwagMigrationOxid',
    'version' => $oxidComposerJson['version'],
];
$swagMigrationAssistant = [
    'autoload' => $assistantComposerJson['autoload'],
    'baseClass' => SwagMigrationAssistant::class,
    'managedByComposer' => false,
    'active' => true,
    'path' => $assistantPluginRootPath,
    'name' => 'SwagMigrationAssistant',
    'version' => $assistantComposerJson['version'],
];
$pluginLoader = new StaticKernelPluginLoader($classLoader, null, [$swagMigrationAssistant, $swagMigrationOxid]);

$kernel = new Kernel('dev', true, $pluginLoader, $shopwareVersion);
$kernel->boot();
$projectDir = $kernel->getProjectDir();
$cacheDir = $kernel->getCacheDir();

$relativeCacheDir = \str_replace($projectDir, '', $cacheDir);

$phpStanConfigDist = \file_get_contents(__DIR__ . '/../phpstan.neon.dist');
if ($phpStanConfigDist === false) {
    throw new RuntimeException('phpstan.neon.dist file not found');
}

// because the cache dir is hashed by Shopware, we need to set the PHPStan config dynamically
$phpStanConfig = \str_replace(
    [
        "\n        # the placeholder \"%ShopwareHashedCacheDir%\" will be replaced on execution by bin/phpstan-config-generator.php script",
        '%ShopwareHashedCacheDir%',
    ],
    [
        '',
        $relativeCacheDir,
    ],
    $phpStanConfigDist
);

\file_put_contents(__DIR__ . '/../phpstan.neon', $phpStanConfig);
