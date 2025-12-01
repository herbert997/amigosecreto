<?php
// admin.php
require 'helpers.php';
$pdo = db();
$groupId = (int)($_GET['group'] ?? 0);
$token = $_GET['token'] ?? '';

if(!$groupId || !$token) exit('Parâmetros inválidos');

$stmt = $pdo->prepare('SELECT * FROM `groups` WHERE id=? AND owner_token=?');
$stmt->execute([$groupId, $token]);
$group = $stmt->fetch();
if(!$group) exit('Grupo não encontrado ou token inválido');

// Handlers: add, delete, edit
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['add_name'])){
        $name = trim($_POST['add_name']); $email = trim($_POST['add_email'] ?? '');
        if($name){ $ins = $pdo->prepare('INSERT INTO participants (group_id,name,email) VALUES (?,?,?)'); $ins->execute([$groupId,$name,$email]); }
    }
    if(isset($_POST['delete_id'])){
        $del = $pdo->prepare('DELETE FROM participants WHERE id=? AND group_id=?'); $del->execute([ (int)$_POST['delete_id'], $groupId]);
    }
    if(isset($_POST['edit_id'])){
        $eid = (int)$_POST['edit_id']; $ename = trim($_POST['edit_name']); $eemail = trim($_POST['edit_email']);
        $up = $pdo->prepare('UPDATE participants SET name=?, email=? WHERE id=? AND group_id=?'); $up->execute([$ename,$eemail,$eid,$groupId]);
    }
    header('Location: '.$_SERVER['REQUEST_URI']); exit;
}

$parts = $pdo->prepare('SELECT * FROM participants WHERE group_id=? ORDER BY id'); $parts->execute([$groupId]);
$rows = $parts->fetchAll();
$publicBase = base_url("invite.php?group={$groupId}&t=");
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin - Amigo Secreto — Grupo Estrela do Amanhã</title>
<link rel="stylesheet" href="assets/style.css">
</head><body>
<div class="container">
  <div class="header">
    <h1>Amigo Secreto — Grupo Estrela do Amanhã</h1>
    <div><strong>Admin</strong></div>
  </div>

  <p>Link público base: <code><?= h($publicBase) ?>ID</code></p>

  <h3>Participantes</h3>
  <table>
    <tr><th>ID</th><th>Nome</th><th>Email</th><th>Já sorteou</th><th>Ações</th></tr>
    <?php foreach($rows as $r): ?>
    <tr>
      <td><?= $r['id'] ?></td>
      <td><?= h($r['name']) ?></td>
      <td><?= h($r['email']) ?></td>
      <td><?= $r['drawn'] ? 'Sim' : 'Não' ?></td>
      <td>
        <form style="display:inline" method="post"><input type="hidden" name="delete_id" value="<?=$r['id']?>"><button class="small">Deletar</button></form>
        <form style="display:inline" method="post" onsubmit="return editPrompt(this);">
            <input type="hidden" name="edit_id" value="<?=$r['id']?>">
            <input type="hidden" name="edit_name">
            <input type="hidden" name="edit_email">
            <button class="small">Editar</button>
        </form>
        <a class="small" target="_blank" href="<?= $publicBase . $r['id'] ?>">Abrir link participante</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>

  <h3>Adicionar participante</h3>
  <form method="post">
    <input name="add_name" placeholder="Nome" required>
    <input name="add_email" placeholder="Email (opcional)">
    <button type="submit">Adicionar</button>
  </form>

  <script>
  function editPrompt(form){
    var name = prompt('Novo nome', '');
    if(name===null) return false;
    var email = prompt('Novo email (opcional)', '');
    if(email===null) return false;
    form.querySelector('input[name="edit_name"]').value = name;
    form.querySelector('input[name="edit_email"]').value = email;
    return true;
  }
  </script>
</div>
</body></html>
