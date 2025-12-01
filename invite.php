<?php
// invite.php
require 'helpers.php';
$pdo = db();
$groupId = (int)($_GET['group'] ?? 0);
$participantId = (int)($_GET['t'] ?? 0);

if(!$groupId || !$participantId) exit('Link inválido');

// carregar participante e grupo
$part = $pdo->prepare('SELECT * FROM participants WHERE id=? AND group_id=?'); $part->execute([$participantId,$groupId]);
$p = $part->fetch();
if(!$p) exit('Participante não encontrado');

$group = $pdo->prepare('SELECT * FROM `groups` WHERE id=?'); $group->execute([$groupId]); $g = $group->fetch();
if(!$g) exit('Grupo não encontrado');

?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Sortear - Amigo Secreto — Grupo Estrela do Amanhã</title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<div class="container">
  <div class="header">
    <h1>Amigo Secreto — Grupo Estrela do Amanhã</h1>
  </div>

  <h2>Olá, <?= h($p['name']) ?></h2>

  <?php if($p['drawn']): 
      $recv = $pdo->prepare('SELECT name,email FROM participants WHERE id=?'); $recv->execute([$p['drawn_id']]); $r = $recv->fetch();
  ?>
      <div class="notice">Você já realizou o sorteio. Seu amigo secreto é: <strong><?= h($r['name']) ?></strong></div>
      <p><a class="small" href="view_result.php?group=<?= $groupId ?>&user=<?= $participantId ?>">Ver meu resultado</a></p>
  <?php else: ?>
      <p>Clique no botão abaixo para sortear seu amigo secreto. Você só pode sortear uma vez e não pode tirar você mesmo.</p>
      <form method="post" action="draw.php">
        <input type="hidden" name="group" value="<?= $groupId ?>">
        <input type="hidden" name="participant" value="<?= $participantId ?>">
        <button type="submit">Sortear agora</button>
      </form>
  <?php endif; ?>
</div>
</body></html>
