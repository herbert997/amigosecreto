<?php
// create_group.php
require 'helpers.php';
$pdo = db();
$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $groupName = trim($_POST['group_name'] ?? '');
    $namesText = trim($_POST['names'] ?? '');
    if(!$namesText){
        $message = 'Insira ao menos um nome na lista.';
    } else {
        $ownerToken = genToken(40);
        $publicToken = genToken(16);
        $stmt = $pdo->prepare('INSERT INTO `groups` (name, owner_token, public_token) VALUES (?, ?, ?)');
        $stmt->execute([$groupName, $ownerToken, $publicToken]);
        $groupId = $pdo->lastInsertId();

        $lines = preg_split('/\r?\n/', $namesText);
        $ins = $pdo->prepare('INSERT INTO participants (group_id, name, email) VALUES (?, ?, ?)');
        foreach($lines as $line){
            $parts = array_map('trim', explode(',', $line));
            $name = $parts[0] ?? '';
            $email = $parts[1] ?? null;
            if($name){ $ins->execute([$groupId, $name, $email]); }
        }

        $adminUrl = base_url("admin.php?group={$groupId}&token={$ownerToken}");
        $publicBase = base_url("invite.php?group={$groupId}&t=");
        $message = "Grupo criado com sucesso!<br>
            <strong>Link admin:</strong> <a href=\"{$adminUrl}\">{$adminUrl}</a><br>
            <strong>Link público base (use o ID do participante no final):</strong> <code>{$publicBase}ID_DO_PARTICIPANTE</code>";
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Amigo Secreto — Grupo Estrela do Amanhã</title>
<link rel="stylesheet" href="assets/style.css">
</head><body>
<div class="container">
  <div class="header">
    <h1>Amigo Secreto — Grupo Estrela do Amanhã</h1>
  </div>

  <?php if($message): ?>
    <div class="notice"><?= $message ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="form-row">
      <label>Nome do grupo (opcional)<br>
      <input type="text" name="group_name" placeholder="Ex: Amigo Secreto - Turma A"></label>
    </div>

    <div class="form-row">
      <label>Lista de participantes (uma por linha). Opcional: separar e-mail por vírgula.<br>
      <textarea name="names" rows="8" placeholder="João Silva,joao@ex.com
Maria"></textarea></label>
    </div>

    <div class="form-row">
      <button type="submit">Criar grupo e gerar links</button>
    </div>
  </form>

  <p class="small">Após criar, compartilhe os links individuais com cada participante. O administrador poderá editar/excluir nomes.</p>
</div>
</body></html>
