<?php
final class Blocks {
  public function __construct(private PDO $pdo) {}
  public function byRegion(string $region): array {
    $st=$this->pdo->prepare("SELECT * FROM content_blocks WHERE region=? AND visible=1 ORDER BY position ASC, id ASC");
    $st->execute([$region]); return $st->fetchAll();
  }
  public function all(): array {
    return $this->pdo->query("SELECT * FROM content_blocks ORDER BY region, position, id")->fetchAll();
  }
  public function find(int $id): ?array {
    $st=$this->pdo->prepare("SELECT * FROM content_blocks WHERE id=?");
    $st->execute([$id]); $r=$st->fetch(); return $r?:null;
  }
  public function save(array $d): int {
    $visible = !empty($d['visible']) ? 1 : 0;
    $pos = (int)($d['position'] ?? 0);
    if (!empty($d['id'])) {
      $st=$this->pdo->prepare("UPDATE content_blocks SET region=?,title=?,body_html=?,position=?,visible=?,updated_at=CURRENT_TIMESTAMP WHERE id=?");
      $st->execute([$d['region'],$d['title'],$d['body_html'],$pos,$visible,(int)$d['id']]);
      return (int)$d['id'];
    } else {
      $st=$this->pdo->prepare("INSERT INTO content_blocks(region,title,body_html,position,visible) VALUES(?,?,?,?,?)");
      $st->execute([$d['region'],$d['title'],$d['body_html'],$pos,$visible]);
      return (int)$this->pdo->lastInsertId();
    }
  }
  public function delete(int $id): void {
    $st=$this->pdo->prepare("DELETE FROM content_blocks WHERE id=?");
    $st->execute([$id]);
  }
  public function move(int $id, int $delta): void {
    $blk = $this->find($id); if(!$blk) return;
    $newPos = max(0, (int)$blk['position'] + $delta);
    $this->pdo->beginTransaction();
    $swap=$this->pdo->prepare("UPDATE content_blocks SET position=? WHERE region=? AND position=? AND id!=?");
    $swap->execute([(int)$blk['position'],$blk['region'],$newPos,$id]);
    $upd=$this->pdo->prepare("UPDATE content_blocks SET position=? WHERE id=?");
    $upd->execute([$newPos,$id]);
    $this->pdo->commit();
  }
}
