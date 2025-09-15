<?php
require_once __DIR__ . '/cors.php';
require __DIR__ . '/../vendor/autoload.php';

use App\classes\Token;
use App\classes\Cuisine;
use App\classes\Food;
use App\classes\Cart;
use App\classes\CartItem;
use App\classes\Order;
use App\classes\OrderItem;
use App\classes\Auth;


$token = new Token();
$token->createTable();
echo "Token table created.\n";


$cuisine = new Cuisine();
$cuisine->createTable();
echo "Cuisine table created.\n";

$food = new Food();
$food->createTable();
echo "Food table created.\n";

$cart = new Cart();
$cart->create_table();
echo "Cart table created.\n";


$cartItem = new CartItem();
$cartItem->create_table();
echo "CartItem table created.\n";


$order = new Order();
$order->createTable();
echo "Order table created.\n";


$orderItem = new OrderItem();
$orderItem->createTable();
echo "OrderItem table created.\n";

$auth = new Auth();
$auth->create_table();
echo "User table created.\n";

echo "All tables created successfully!\n";
