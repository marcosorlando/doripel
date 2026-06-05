<?php

namespace App\Conn;

use PDO;
use PDOException;
use PDOStatement;

use function array_keys;
use function count;
use function implode;
use function reset;
use function rtrim;
use function sprintf;
use function str_repeat;
use function trigger_error;

/**
 * Classe responsável por cadastros genéricos no banco de dados.
 */
class Create
{
    private string $tabela;

    /**
     * @var array<string, mixed>|list<mixed>
     */
    private array $dados = [];

    private mixed $result = null;

    private ?PDOStatement $statement = null;

    private string $query = '';

    private readonly PDO $conn;

    public function __construct()
    {

        $this->conn = Conn::getConn();
    }

    /**
     * Executa um cadastro simplificado no banco de dados utilizando prepared statements.
     *
     * @param array<string, mixed> $dados
     */
    public function exeCreate(string $tabela, array $dados): void
    {

        $this->tabela = $tabela;
        $this->dados = $dados;

        $this->getSyntax();
        $this->statement = null;
        $this->execute();
    }

    // Cria a sintaxe da query para Prepared Statements

    private function getSyntax(): void
    {

        $fields = implode(', ', array_keys($this->dados));
        $places = ':' . implode(', :', array_keys($this->dados));
        $this->query = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->tabela, $fields, $places);
    }

    // Executa a query!

    private function execute(): void
    {

        $this->connect();

        try {
            $this->statement?->execute($this->dados);
            $this->result = $this->conn->lastInsertId();
        } catch (PDOException $pdoException) {
            $this->result = null;
            // Substitua por seu logger ou tratamento de erro
            trigger_error('Erro ao cadastrar: ' . $pdoException->getMessage(), E_USER_WARNING);
        }
    }

    private function connect(): void
    {

        if (!$this->statement instanceof PDOStatement) {
            $this->statement = $this->conn->prepare($this->query);
        }
    }

    /**
     * Executa um cadastro múltiplo no banco de dados utilizando prepared statements.
     *
     * @param array<int|string, array<int|string, mixed>> $dados
     */
    public function exeCreateMulti(string $tabela, array $dados): void
    {

        $this->tabela = $tabela;

        if ([] === $dados) {
            $this->result = null;
            trigger_error('Dados inválidos para cadastro múltiplo.', E_USER_WARNING);

            return;
        }

        $firstRow = reset($dados);

        $fields = implode(', ', array_keys($firstRow));
        $places = '';
        $inserts = [];
        $links = count(array_keys($firstRow));

        foreach ($dados as $valueMult) {
            $places .= '(' . rtrim(str_repeat('?,', $links), ',') . '),';
            foreach ($valueMult as $valueSingle) {
                $inserts[] = $valueSingle;
            }
        }

        $places = rtrim($places, ',');
        $this->dados = $inserts;
        $this->query = sprintf('INSERT INTO %s (%s) VALUES %s', $this->tabela, $fields, $places);
        $this->statement = null;
        $this->execute();
    }

    // Obtém o PDO e prepara a query

    /**
     * Retorna o ID do registro inserido ou null caso nenhum registro seja inserido.
     */
    public function getResult(): mixed
    {

        return $this->result;
    }
}
