<?php

namespace App\Conn;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

use function is_array;
use function is_scalar;
use function is_string;
use function parse_str;
use function reset;
use function sprintf;

/**
 * Classe responsável por deletar genéricamente no banco de dados.
 */
class Delete
{
    private string $tabela;

    private string $termos;

    private string $sql;

    /**
     * @var array<string, null|scalar>
     */
    private array $places = [];

    private ?bool $result = null;

    private ?PDOStatement $statement = null;

    private readonly PDO $conn;

    public function __construct()
    {

        $this->conn = Conn::getConn();
    }

    public function exeDelete(string $tabela, string $termos, string $parseString): void
    {

        $this->tabela = $tabela;
        $this->termos = $termos;

        $parsed = [];
        parse_str($parseString, $parsed);
        $this->places = $this->normalizeValues($parsed);
        $this->getSyntax();
        $this->statement = null;
        $this->execute();
    }

    /**
     * @param array<int|string, mixed> $values
     *
     * @return array<string, null|scalar>
     */
    private function normalizeValues(array $values): array
    {

        $normalized = [];
        foreach ($values as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            if (is_scalar($value) || null === $value) {
                $normalized[$key] = $value;

                continue;
            }
            // parse_str pode gerar arrays quando há parâmetros repetidos; aqui pegamos o primeiro valor.
            if (is_array($value)) {
                $first = reset($value);
                $normalized[$key] = is_scalar($first) || null === $first ? $first : null;
            } else {
                $normalized[$key] = null;
            }
        }

        return $normalized;
    }

    /**
     * Monta a sintaxe da query para Prepared Statements.
     */
    private function getSyntax(): void
    {

        $this->sql = sprintf('DELETE FROM %s %s', $this->tabela, $this->termos);
    }

    /**
     * Executa a query!
     */
    private function execute(): void
    {

        $this->connect();

        try {
            $this->statement?->execute($this->places);
            $this->result = true;
        } catch (PDOException $pdoException) {
            $this->result = false;
            throw new RuntimeException('Erro ao deletar: ' . $pdoException->getMessage(), 0, $pdoException);
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

    public function getResult(): ?bool
    {

        return $this->result;
    }

    public function getRowCount(): int
    {

        return $this->statement?->rowCount() ?? 0;
    }

    public function setPlaces(string $parseString): void
    {

        $parsed = [];
        parse_str($parseString, $parsed);
        $this->places = $this->normalizeValues($parsed);
        $this->getSyntax();
        $this->statement = null;
        $this->execute();
    }
}
