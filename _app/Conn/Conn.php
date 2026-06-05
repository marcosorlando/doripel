<?php

declare(strict_types=1);

namespace App\Conn;

use Dotenv\Dotenv;
use PDO;
use PDOException;

use function constant;
use function defined;
use function is_string;
use function sprintf;

Dotenv::createImmutable(__DIR__ . '/../Config/')->load();

/**
 * Conn.class [ CONEXÃO ]
 * Classe abstrata de conexão. Padrão Singleton.
 * Retorna um objeto PDO pelo método estático `getConn`.
 */
class Conn
{
    private static ?PDO $Connect = null;

    /**
     * Construtor protegido para prevenir criação de instâncias diretas.
     */
    private function __construct() {}

    /**
     * Método público para obter a conexão PDO criada.
     */
    public static function getConn(): PDO
    {

        return self::conectar();
    }

    /**
     * Método responsável por estabelecer a conexão com o banco de dados.
     * As configurações são obtidas das variáveis de ambiente carregadas do arquivo .env.
     * @throws \PDOException
     */
    private static function conectar(): PDO
    {

        if (!self::$Connect instanceof PDO) {
            try {
                // Obtendo informações do banco de dados do ambiente
                $dbCharsetValue = $_ENV['DB_CHARSET'] ?? null;
                $dbCharset = is_string($dbCharsetValue) && '' !== $dbCharsetValue ? $dbCharsetValue : 'utf8mb4';

                $dbCollationValue = $_ENV['DB_COLLATION'] ?? null;
                $dbCollation = is_string($dbCollationValue) && '' !== $dbCollationValue
                    ? $dbCollationValue
                    : 'utf8mb4_unicode_ci';

                $dbDriverValue = $_ENV['DB_DRIVER'] ?? null;
                $dbDriver = is_string($dbDriverValue) && '' !== $dbDriverValue ? $dbDriverValue : 'mysql';

                $dbPortValue = $_ENV['DB_PORT'] ?? null;
                $dbPort = is_string($dbPortValue) && '' !== $dbPortValue ? $dbPortValue : '3306';

                $dbName = '';
                if (defined('SIS_DB_NAME')) {
                    $dbNameValue = constant('SIS_DB_NAME');
                    if (is_string($dbNameValue)) {
                        $dbName = $dbNameValue;
                    }
                }

                $dbHost = 'localhost';
                if (defined('SIS_DB_HOST')) {
                    $dbHostValue = constant('SIS_DB_HOST');
                    if (is_string($dbHostValue) && '' !== $dbHostValue) {
                        $dbHost = $dbHostValue;
                    }
                }

                $dbUser = '';
                if (defined('SIS_DB_USER')) {
                    $dbUserValue = constant('SIS_DB_USER');
                    if (is_string($dbUserValue)) {
                        $dbUser = $dbUserValue;
                    }
                }

                $dbPass = '';
                if (defined('SIS_DB_PASS')) {
                    $dbPassValue = constant('SIS_DB_PASS');
                    if (is_string($dbPassValue)) {
                        $dbPass = $dbPassValue;
                    }
                }

                $dsn = sprintf(
                    '%s:host=%s;port=%s;dbname=%s;charset=%s',
                    $dbDriver,
                    $dbHost,
                    $dbPort,
                    $dbName,
                    $dbCharset
                );

                // Definindo opções do PDO
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO\Mysql::ATTR_INIT_COMMAND => sprintf(
                        'SET NAMES %s; SET collation_connection = %s',
                        $dbCharset,
                        $dbCollation
                    ),
                ];

                // Criando a instância de conexão
                self::$Connect = new PDO(
                    $dsn,
                    $dbUser,
                    $dbPass,
                    $options
                );
            } catch (PDOException $e) {
                // Lançando uma exceção detalhada em caso de erro
                throw new PDOException(
                    sprintf(
                        'Erro ao conectar ao banco: [%d] %s em %s (linha %d)',
                        $e->getCode(),
                        $e->getMessage(),
                        $e->getFile(),
                        $e->getLine()
                    ),
                    $e->getCode(),
                    $e
                );
            }
        }

        return self::$Connect;
    }

    /**
     * Método wakeup privado para prevenir desserialização.
     */
    public function __wakeup(): void {}

    /**
     * Método clone privado para prevenir clonagem da classe.
     */
    private function __clone() {}
}
