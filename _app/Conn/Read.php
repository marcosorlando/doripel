<?php

declare(strict_types=1);

namespace App\Conn;

use PDO;
use PDOException;
use PDOStatement;
use Stringable;

use function array_diff;
use function array_keys;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_null;
use function is_string;
use function json_encode;
use function parse_str;
use function preg_match_all;
use function reset;
use function sprintf;
use function strtolower;

class Read
{
    private string $Select = '';

    /**
     * @var null|array<string, null|bool|int|string>
     */
    private ?array $Places = null;

    /**
     * @var null|array<int, array<string, mixed>>
     */
    private ?array $Result = null; // Resultado da query

    private ?PDOStatement $Read = null;

    // Representa a declaração preparada
    private readonly PDO $Conn; // Conexão

    /**
     * Inicializa a conexão com o banco de dados.
     * @throws \RuntimeException quando a conexão não pode ser estabelecida
     */
    public function __construct()
    {

        $this->Conn = Conn::getConn();
    }

    /**
     * Obtém dados de uma tabela relacionada, com base na coluna e valor fornecidos.
     *
     * @param string $Tabela nome da tabela
     * @param string $Coluna nome da coluna de referência
     * @param int|string $Valor valor associado
     * @param null|string $Campos colunas desejadas (opcional)
     *
     * @return array<string, mixed>|false array com resultados ou falso se não encontrar
     */
    public function linkResult(string $Tabela, string $Coluna, int|string $Valor, ?string $Campos = null): array|false
    {

        if (null !== $Campos && '' !== $Campos) {
            $this->fullRead(
                sprintf('SELECT %s FROM %s WHERE %s = :value', $Campos, $Tabela, $Coluna),
                'value=' . $Valor
            );
        } else {
            $this->exeRead($Tabela, sprintf('WHERE %s = :value', $Coluna), 'value=' . $Valor);
        }

        if (null !== $this->Result && [] !== $this->Result) {
            return $this->Result[0];
        }

        return false;
    }

    /**
     * Executa uma query completa, permitindo queries personalizadas.
     *
     * @param string $Query query SQL pronta
     * @param null|string $ParseString parâmetros chave/valor (opcional)
     */
    public function fullRead(string $Query, ?string $ParseString = null): void
    {

        $this->Select = $Query;
        $this->Places = null !== $ParseString && '' !== $ParseString ? $this->normalizePlaces($ParseString) : null;

        $this->execute();
    }

    public function setPlaces($ParseString) {
        parse_str($ParseString, $this->Places);
        $this->Execute();
    }

    /**
     * Normaliza uma string de parâmetros no formato querystring em um array tipado.
     * @return array<string, null|bool|int|string>
     */
    private function normalizePlaces(string $parseString): array
    {

        $parsed = [];
        parse_str($parseString, $parsed);

        return $this->sanitizePlaceArray($parsed);
    }

    /**
     * @param array<int|string, mixed> $values
     *
     * @return array<string, null|bool|int|string>
     */
    private function sanitizePlaceArray(array $values): array
    {

        $normalized = [];
        foreach ($values as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $normalized[$key] = self::sanitizeValue($value);
        }

        if (isset($normalized['limit'])) {
            $normalized['limit'] = (int)$normalized['limit'];
        }

        if (isset($normalized['offset'])) {
            $normalized['offset'] = (int)$normalized['offset'];
        }

        return $normalized;
    }

    private static function sanitizeValue(mixed $value): bool|int|string|null
    {

        if (is_array($value)) {
            $first = reset($value);
            if (false === $first) {
                return null;
            }

            return self::sanitizeValue($first);
        }

        if (is_int($value) || is_bool($value) || is_string($value) || null === $value) {
            return $value;
        }

        if (is_float($value)) {
            return (string)$value;
        }

        if ($value instanceof Stringable) {
            return (string)$value;
        }

        return null;
    }

    /**
     * Prepara e executa a consulta SQL.
     */
    private function execute(): void
    {

        try {
            $this->Read = $this->Conn->prepare($this->Select);

            if (null !== $this->Places) {
                foreach ($this->Places as $key => $value) {
                    $paramType = PDO::PARAM_STR; // Padrão para string
                    if (is_int($value)) {
                        $paramType = PDO::PARAM_INT;
                    } elseif (is_bool($value)) {
                        $paramType = PDO::PARAM_BOOL;
                    } elseif (is_null($value)) {
                        $paramType = PDO::PARAM_NULL;
                    }

                    // Especificamente para 'limit' e 'offset', garantir que sejam inteiros
                    // A conversão (int) já foi feita em fullRead/exeRead, mas aqui reforçamos o bind como PARAM_INT
                    if (
                        'limit' === strtolower($key)
                        || 'offset' === strtolower($key)
                    ) {
                        $this->Read->bindValue(':' . $key, (int)$value, PDO::PARAM_INT);
                    } else {
                        $this->Read->bindValue(':' . $key, $value, $paramType);
                    }
                }

                $this->Read->execute(); // Linha 192 original, agora sem passar $this->Places diretamente
            } else {
                $this->Read->execute();
            }

            /**
             * @var array<int, array<string, mixed>> $result
             */
            $result = $this->Read->fetchAll(PDO::FETCH_ASSOC);
            $this->Result = $result;
        } catch (PDOException $pdoException) {
            $placesJson = null !== $this->Places ? json_encode($this->Places, JSON_UNESCAPED_UNICODE) : 'null';
            $errorMsg = sprintf(
                'Erro ao executar a consulta: %s (SQL: %s, Places: %s)',
                $pdoException->getMessage(),
                $this->Select,
                $placesJson // Adiciona os Places ao erro
            );

            throw new PDOException($errorMsg, (int)$pdoException->getCode(), $pdoException); // Linha 203 original
        }
    }

    /**
     * Executa queries simples sobre a tabela especificada.
     *
     * @param string $Tabela nome da tabela
     * @param null|string $Termos filtros e condições da consulta
     * @param null|string $ParseString parâmetros chave/valor (string formatada)
     */
    public function exeRead(string $Tabela, ?string $Termos = null, ?string $ParseString = null): void
    {

        if (null !== $ParseString && '' !== $ParseString) {
            $this->Places = $this->normalizePlaces($ParseString);

            if (null !== $Termos) {
                preg_match_all('/:([a-zA-Z_]\w*)/', $Termos, $matches);
                $placeholders = $matches[1];
                $placeKeys = array_keys($this->Places);
                if ([] !== $placeholders && [] !== array_diff($placeholders, $placeKeys)) {
                    throw new PDOException('Parâmetros informados não correspondem aos placeholders na consulta SQL!');
                }
            }
        } else {
            $this->Places = null;
        }

        $this->Select = sprintf('SELECT * FROM %s %s', $Tabela, $Termos);
        $this->execute();
    }

    /**
     * Obtém o resultado da última consulta.
     * @return null|array<int, array<string, mixed>> resultado da consulta ou null
     */
    public function getResult(): ?array
    {

        return $this->Result;
    }

    /**
     * Retorna o número de linhas afetadas pela última consulta.
     */
    public function getRowCount(): int
    {

        return $this->Read instanceof PDOStatement ? $this->Read->rowCount() : 0;
    }

    /**
     * Define manualmente a query SQL a ser executada.
     * Útil para queries complexas que não se encaixam em exeRead ou fullRead.
     *
     * @param null|array<string, null|bool|int|string> $Places
     */
    public function setQuery(string $Query, ?array $Places = null): void
    {

        $this->Select = $Query;
        $this->Places = null !== $Places ? $this->sanitizePlaceArray($Places) : null;

        $this->execute();
    }
}
