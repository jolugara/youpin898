<?php

// Incluir la clase y la configuración
require_once 'UUAccount.php';
require_once 'config.php';

// Crear una instancia de la cuenta usando el token
$account = new UUAccount($token);

// Obtener y mostrar el nickname del usuario
echo 'Nickname del usuario: ' . $account->get_user_nickname() . "\n";

// Obtener y mostrar la lista de ventas pendientes
$wait_deliver_list = $account->get_wait_deliver_list();
echo "Lista de ventas pendientes:\n";
foreach ($wait_deliver_list as $order) {
    echo "Order ID: " . $order['order_id'] . " - Item: " . $order['item_name'] . "\n";
}
