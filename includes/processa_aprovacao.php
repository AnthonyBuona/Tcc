<?php
// Este arquivo foi removido. Use includes/processar_usuario.php para aprovar/reprovar usuários.
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Este endpoint foi removido. Utilize includes/processar_usuario.php.'
]);
?>