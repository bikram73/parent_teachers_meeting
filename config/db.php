<?php
declare(strict_types=1);

final class SupabaseResult
{
    public int $num_rows;
    private array $rows;
    private int $cursor = 0;

    public function __construct(array $rows = [])
    {
        $this->rows = array_values($rows);
        $this->num_rows = count($this->rows);
    }

    public function fetch_assoc(): ?array
    {
        if ($this->cursor >= $this->num_rows) {
            return null;
        }

        return $this->rows[$this->cursor++];
    }
}

final class SupabaseStatement
{
    private PDOStatement $statement;
    private array $boundParams = [];
    public int $affected_rows = 0;
    public string $error = '';

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    public function bind_param(string $types, &...$vars): bool
    {
        $this->boundParams = $vars;
        return true;
    }

    public function execute(?array $params = null): bool
    {
        try {
            $parameters = $params ?? $this->boundParams;
            $this->statement->execute(array_values($parameters));
            $this->affected_rows = $this->statement->rowCount();
            return true;
        } catch (Throwable $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    public function get_result(): SupabaseResult|false
    {
        try {
            return new SupabaseResult($this->statement->fetchAll(PDO::FETCH_ASSOC));
        } catch (Throwable $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    public function close(): void
    {
    }
}

final class SupabaseConnection
{
    private PDO $pdo;
    public ?string $connect_error = null;
    public string $error = '';

    public function __construct()
    {
        $host = $this->firstEnv(['SUPABASE_DB_HOST', 'PGHOST', 'DB_HOST'], '');
        $port = $this->firstEnv(['SUPABASE_DB_PORT', 'PGPORT', 'DB_PORT'], '5432');
        $database = $this->firstEnv(['SUPABASE_DB_NAME', 'PGDATABASE', 'DB_NAME'], 'postgres');
        $username = $this->firstEnv(['SUPABASE_DB_USER', 'PGUSER', 'DB_USERNAME'], 'postgres');
        $password = $this->firstEnv(['SUPABASE_DB_PASSWORD', 'PGPASSWORD', 'DB_PASSWORD'], '');
        $sslmode = $this->firstEnv(['SUPABASE_DB_SSLMODE', 'PGSSLMODE', 'DB_SSLMODE'], 'prefer');

        if ($host === '') {
            $this->connect_error = 'Missing database host configuration.';
            $this->error = $this->connect_error;
            throw new RuntimeException($this->connect_error);
        }

        try {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
                $host,
                $port,
                $database,
                $sslmode
            );

            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (Throwable $exception) {
            $this->connect_error = $exception->getMessage();
            $this->error = $exception->getMessage();
            throw new RuntimeException($exception->getMessage(), 0, $exception);
        }
    }

    private function firstEnv(array $keys, string $default = ''): string
    {
        foreach ($keys as $key) {
            $value = getenv($key);
            if ($value !== false && $value !== '') {
                return $value;
            }
        }

        return $default;
    }

    public function prepare(string $sql): SupabaseStatement|false
    {
        try {
            return new SupabaseStatement($this->pdo->prepare($sql));
        } catch (Throwable $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    public function query(string $sql): SupabaseResult|bool
    {
        try {
            $statement = $this->pdo->query($sql);
            if ($statement === false) {
                return false;
            }

            if ($statement->columnCount() > 0) {
                return new SupabaseResult($statement->fetchAll(PDO::FETCH_ASSOC));
            }

            return true;
        } catch (Throwable $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    public function real_escape_string(string $value): string
    {
        return addslashes($value);
    }

    public function begin_transaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    public function select_db(string $database): bool
    {
        return true;
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}

$conn = new SupabaseConnection();
?>
