<?php
// view_result.php
require 'helpers.php';
$pdo = db();
$groupId = (int)($_GET['group'] ?? 0);
$participantId = (int)($_GET['user'] ?? 0);

if(!$groupId || !$participantId) exit('Parâmetros inválidos');

$stmt = $pdo->prepare('SELECT * FROM participants WHERE id=? AND group_id=?');
$stmt->execute([$participantId, $groupId]);
$p = $stmt->fetch();
if(!$p) exit('Participante inválido');

if(!$p['drawn']){
    echo '<p>Você ainda não sorteou.</p>';
    exit;
}

$r = $pdo->prepare('SELECT name,email FROM participants WHERE id=?'); $r->execute([$p['drawn_id']]); $r = $r->fetch();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Resultado - Amigo Secreto — Grupo Estrela do Amanhã</title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<div class="container">
  <div class="header"><h1>Amigo Secreto — Grupo Estrela do Amanhã</h1></div>

  <div class="notice">Seu amigo secreto é: <strong><?= h($r['name']) ?></strong></div>
  <?php if(!empty($r['email'])): ?>
    <p>Contato: <?= h($r['email']) ?></p>
  <?php endif; ?>
</div>
</body></html>
