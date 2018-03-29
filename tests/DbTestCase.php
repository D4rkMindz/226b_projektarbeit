<?php

namespace App\Test;

use Cake\Database\Connection;
use Exception;
use PDO;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\Operation\Factory;
use PHPUnit\DbUnit\TestCaseTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Container;

/**
 * Class DbTestCase.
 */
abstract class DbTestCase extends ApiTestCase
{
    use TestCaseTrait;

    /**
     * @var PDO
     */
    private static $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    /**
     * @var Container
     */
    private $container;

    /**
     * Get PDO object.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return PDO
     */
    protected function getPdo(): PDO
    {
        if (!self::$pdo) {
            $pdo = $this->app->getContainer()->get(Connection::class)->getDriver()->connection();
            self::$pdo = $pdo;
        }

        return self::$pdo;
    }

    /**
     * Setup.
     *
     * This code will be executed once before the tests are executed.
     *
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp()
    {
        parent::setUp();
        $this->container = $this->app->getContainer();

        // Check if phinxlog table exists in database.
        $tableSchema = $this->container->get('settings')->get('db')['database'];
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare('SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = :tableschema AND TABLE_NAME = :phinxlog');
        $stmt->execute(['tableschema' => $tableSchema, 'phinxlog' => 'phinxlog']);

        $shouldMigrate = true;

        if ($stmt->fetch()) {
            $shouldMigrate = $this->hasPendingMigrations($pdo);
        }

        if ($shouldMigrate) {
            chdir(__DIR__ . '/../config');
            $wrap = new TextWrapper(new PhinxApplication());
            // Execute the command and determine if it was successful.
            $target = null;
            call_user_func([$wrap, 'getMigrate'], 'local', $target);
            $error = $wrap->getExitCode() > 0;
            if ($error) {
                throw new Exception('Error: Setup database failed with exit code: %s', $wrap->getExitCode());
            }
        }

        $this->truncateTables();
        Factory::INSERT()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * Check if there is any pending migration.
     *
     * @param PDO $pdo
     *
     * @throws \Interop\Container\Exception\ContainerException
     *
     * @return bool
     */
    private function hasPendingMigrations(PDO $pdo): bool
    {
        // Check if there is any pending migration
        $stmt = $pdo->prepare('SELECT version FROM phinxlog ORDER BY version ASC');
        $stmt->execute();
        $phinxlogRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($phinxlogRows)) {
            return false;
        }

        $migratedVersions = [];
        foreach ($phinxlogRows as $record) {
            $migratedVersions[$record['version']] = 1;
        }

        $migrationsPath = $this->container->get('settings')->get('migrations');
        $migrationFiles = glob($migrationsPath . '/*.php');
        foreach ($migrationFiles as $key => $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            if ($filename === 'schema') {
                continue;
            }

            // remove everything after _ to get version from filename
            $version = substr($filename, 0, strpos($filename, '_'));
            if (!isset($migratedVersions[$version])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Truncate all Tables.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function truncateTables()
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->query('SHOW TABLES');
        while ($row = $stmt->fetch()) {
            $table = array_values($row)[0];
            if ($table === 'phinxlog') {
                continue;
            }

            $pdo->prepare(sprintf('TRUNCATE TABLE `%s`', $table))->execute();
        }
    }

    /**
     * Get Connection.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return DefaultConnection
     */
    public function getConnection(): DefaultConnection
    {
        if ($this->conn === null) {
            $this->conn = $this->createDefaultDBConnection($this->getPdo());
        }

        return $this->conn;
    }

    /**
     * Generate Update row.
     *
     * @param array $row
     *
     * @return array
     */
    public function generateUpdateRow(array $row): array
    {
        foreach ($row as $key => $value) {
            if (preg_match('/\w*(id)\w*/', $key)) {
                continue;
            }
            $converted = preg_replace('/[ÄÖÜäöüÉÈÀéèà]/', '', $row[$key]);
            $parts = str_split(html_entity_decode($converted));
            sort($parts);
            $row[$key] = implode($parts);
        }

        return $row;
    }
}
