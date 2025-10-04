<?php
final class Db {
  public PDO $pdo;
  public function __construct() {
    $this->pdo = new PDO('sqlite:' . __DIR__ . '/../db/tjw.sqlite', null, null, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_TIMEOUT => 5,
    ]);
    $this->pdo->exec("PRAGMA journal_mode=WAL;");
    $this->pdo->exec("PRAGMA foreign_keys=ON;");
    $this->pdo->exec("PRAGMA synchronous=NORMAL;");
    $this->pdo->exec("PRAGMA busy_timeout=5000;");
    $this->pdo->exec("PRAGMA trusted_schema=OFF;");
  }
}
