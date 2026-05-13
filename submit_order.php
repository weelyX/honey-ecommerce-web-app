<?php

session_start();
include "database.php";

header("Content-Type: application/json; charset=UTF-8");

function starts_good($value) {
    return preg_match("/^[\p{Arabic}a-zA-Z0-9]/u", trim($value));
}

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "يجب تسجيل الدخول قبل إتمام الطلب"], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data["name"] ?? "");
$phone = trim($data["phone"] ?? "");
$city = trim($data["city"] ?? "");
$address = trim($data["address"] ?? "");
$notes = trim($data["notes"] ?? "");
$items = $data["items"] ?? [];

if ($name == "" || $phone == "" || $city == "" || $address == "" || empty($items)) {
    echo json_encode(["success" => false, "message" => "الرجاء تعبئة بيانات الطلب كاملة"], JSON_UNESCAPED_UNICODE);
    exit;
}

foreach ([$name, $phone, $city, $address] as $value) {
    if (!starts_good($value)) {
        echo json_encode(["success" => false, "message" => "لا يمكن أن تبدأ البيانات برمز"], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if ($notes != "" && !starts_good($notes)) {
    echo json_encode(["success" => false, "message" => "لا يمكن أن تبدأ الملاحظات برمز"], JSON_UNESCAPED_UNICODE);
    exit;
}

$total = 0;

foreach ($items as $item) {
    $product_name = trim($item["name"] ?? "");
    $quantity = intval($item["quantity"] ?? 0);
    $unit_price = floatval($item["price"] ?? 0);
    $total_price = floatval($item["total"] ?? 0);

    if (!starts_good($product_name) || $quantity < 1 || $unit_price <= 0 || $total_price <= 0) {
        echo json_encode(["success" => false, "message" => "بيانات المنتجات غير صحيحة"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $total += $total_price;
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO customers (name, phone, city, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $phone, $city, $address);
    $stmt->execute();
    $customer_id = $conn->insert_id;

    $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_price, notes) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $customer_id, $total, $notes);
    $stmt->execute();
    $order_id = $conn->insert_id;

    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)");

    foreach ($items as $item) {
        $product_name = trim($item["name"]);
        $quantity = intval($item["quantity"]);
        $unit_price = floatval($item["price"]);
        $total_price = floatval($item["total"]);

        $stmt->bind_param("isidd", $order_id, $product_name, $quantity, $unit_price, $total_price);
        $stmt->execute();
    }

    $conn->commit();
    echo json_encode(["success" => true, "order_id" => $order_id], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "حدث خطأ أثناء حفظ الطلب"], JSON_UNESCAPED_UNICODE);
}

?>
