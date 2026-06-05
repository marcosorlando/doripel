<?php

namespace App\Conn;

use PDO;
use PDOException;
use PDOStatement;
use Stringable;

use function array_keys;
use function array_merge;
use function implode;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function parse_str;
use function reset;
use function sprintf;
use function trigger_error;

/**
 * Classe responsável por atualizações genéricas no banco de dados.
 */
class Update
{
    private string $tabela;

    /**
     * @var array<string, mixed>
     */
    private array $dados = [];

    private string $termos;

    /**
     * @var array<string, null|scalar>
     */
    private array $places = [];

    private ?bool $result = null;

    private ?PDOStatement $statement = null;

    private string $sql = '';

    private readonly PDO $conn;

    public function __construct()
    {

        $this->conn = Conn::getConn();
    }

    /**
     * Executa uma atualização simplificada com Prepared Statements.
     */
    /**
     * @param array<string, mixed> $dados
     */
    public function exeUpdate(string $tabela, array $dados, string $termos, string $parseString): void
    {

        $this->tabela = $tabela;
        $this->dados = $dados;
        $this->termos = $termos;

        $this->places = $this->normalizePlaces($parseString);
        $this->getSyntax();
        $this->statement = null;
        $this->execute();
    }

    /**
     * @return array<string, null|scalar>
     */
    private function normalizePlaces(string $parseString): array
    {

        $parsed = [];
        parse_str($parseString, $parsed);

        $normalized = [];
        foreach ($parsed as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            if (is_array($value)) {
                $first = reset($value);
                $value = false === $first ? null : $first;
            }

            if (is_int($value) || is_bool($value) || is_string($value) || null === $value) {
                $normalized[$key] = $value;

                continue;
            }

            if (is_float($value)) {
                $normalized[$key] = (string)$value;

                continue;
            }

            if ($value instanceof Stringable) {
                $normalized[$key] = (string)$value;

                continue;
            }

            $normalized[$key] = null;
        }

        return $normalized;
    }

    /**
     * Monta a sintaxe da query para Prepared Statements.
     */
    private function getSyntax(): void
    {

        $places = [];
        foreach (array_keys($this->dados) as $key) {
            $places[] = $key . ' = :' . $key;
        }

        $set = implode(', ', $places);
        $this->sql = sprintf('UPDATE %s SET %s %s', $this->tabela, $set, $this->termos);
    }

    /**
     * Executa a query!
     */
    private function execute(): void
    {

        $this->connect();
        $this->setNull();

        try {
            $params = array_merge($this->dados, $this->places);
            $this->statement?->execute($params);
            $this->result = true;
        } catch (PDOException $pdoException) {
            $this->result = false;
            // Substitua por seu logger ou tratamento de erro
            trigger_error('Erro ao atualizar: ' . $pdoException->getMessage(), E_USER_WARNING);
        }
    }

    /**
     * Prepara a query SQL para execução.
     */
    private function connect(): void
    {

        if (!$this->statement instanceof PDOStatement) {
            $this->statement = $this->conn->prepare($this->sql);
        }
    }

    /**
     * Define valores vazios como NULL.
     */
    private function setNull(): void
    {

        foreach ($this->dados as $key => $value) {
            $this->dados[$key] = ('' === $value ? null : $value);
        }
    }

    /**
     * Retorna TRUE se não ocorrer erros, ou FALSE.
     */
    public function getResult(): ?bool
    {

        return $this->result;
    }

    /**
     * Retorna o número de linhas alteradas no banco.
     */
    public function getRowCount(): int
    {

        return $this->statement?->rowCount() ?? 0;
    }

    /**
     * Atualiza os valores da condição.
     */
    public function setPlaces(string $parseString): void
    {

        $this->places = $this->normalizePlaces($parseString);
        $this->getSyntax();
        $this->statement = null;
        $this->execute();
    }
}
