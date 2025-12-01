<?php
// draw.php
require 'helpers.php';
$pdo = db();
$groupId = (int)($_POST['group'] ?? 0);
$participantId = (int)($_POST['participant'] ?? 0);

if(!$groupId || !$participantId) exit('Parâmetros inválidos');

// Verifica participante e estado
$part = $pdo->prepare('SELECT * FROM participants WHERE id=? AND group_id=? FOR UPDATE');
$pdo->beginTransaction();
$part->execute([$participantId, $groupId]);
$p = $part->fetch();
if(!$p){ $pdo->rollBack(); exit('Participante inválido'); }
if($p['drawn']){ $pdo->commit(); header("Location: invite.php?group={$groupId}&t={$participantId}"); exit; }

// Seleciona candidatos disponíveis (que ainda não foram sorteados e que não são o próprio)
$stmt = $pdo->prepare('SELECT id FROM participants WHERE group_id=? AND id!=? AND id NOT IN (SELECT drawn_id FROM participants WHERE drawn_id IS NOT NULL) FOR UPDATE');
$stmt->execute([$groupId, $participantId]);
$candidates = $stmt->fetchAll(PDO::FETCH_COLUMN);

if(empty($candidates)){
    $pdo->rollBack();
    echo 'Nenhuma opção disponível para sortear. Contate o administrador.';
    exit;
}

// Escolhe aleatoriamente
$pick = $candidates[array_rand($candidates)];

// Salva resultado
$update = $pdo->prepare('UPDATE participants SET drawn=1, drawn_id=? WHERE id=?');
$update->execute([$pick, $participantId]);

$pdo->commit();
header("Location: invite.php?group={$groupId}&t={$participantId}");
exit;
