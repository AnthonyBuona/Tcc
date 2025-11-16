<?php
// Este arquivo foi descontinuado. Use 'includes/processar_usuario.php' para aprovar ou reprovar usuários.
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Este endpoint foi descontinuado. Utilize includes/processar_usuario.php.'
]);
?>