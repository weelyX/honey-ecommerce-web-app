<?php

include "database.php";

$sql = "
SELECT 
    o.id AS order_id,
    c.name,
    c.phone,
    c.city,
    c.address,
    o.total_price,
    o.status,
    o.notes,
    o.created_at,
    GROUP_CONCAT(
        CONCAT(oi.product_name, ' × ', oi.quantity, ' = ', oi.total_price, ' ر.س')
        SEPARATOR '\n'
    ) AS order_products
FROM orders o
JOIN customers c ON o.customer_id = c.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY 
    o.id,
    c.name,
    c.phone,
    c.city,
    c.address,
    o.total_price,
    o.status,
    o.notes,
    o.created_at
ORDER BY o.created_at DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة الطلبات</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f7f3ea;
            color: #2f3b2f;
            direction: rtl;
        }

        .admin-header {
            background-color: #374e39;
            color: white;
            padding: 35px 20px;
            text-align: center;
        }

        .admin-header h1 {
            margin: 0;
            font-size: 36px;
        }

        .admin-header p {
            margin-top: 10px;
            color: #f1d27a;
            font-size: 18px;
        }

        .admin-container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .order-card {
            background-color: white;
            border-radius: 22px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px #00000018;
            border-right: 6px solid #c9a227;
        }

        .order-top {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 15px;
        }

        .order-number {
            font-size: 24px;
            font-weight: bold;
            color: #374e39;
        }

        .order-status {
            background-color: #f1d27a;
            color: #374e39;
            padding: 8px 16px;
            border-radius: 30px;
            font-weight: bold;
        }

        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 18px;
            margin-bottom: 20px;
        }

        .info-box {
            background-color: #faf7ef;
            padding: 15px;
            border-radius: 16px;
        }

        .info-box strong {
            display: block;
            margin-bottom: 8px;
            color: #374e39;
        }

        .products-box {
            background-color: #f7f3ea;
            padding: 18px;
            border-radius: 16px;
            line-height: 1.9;
            white-space: pre-line;
        }

        .total {
            margin-top: 20px;
            font-size: 22px;
            font-weight: bold;
            color: #c1871a;
        }

        .empty {
            background-color: white;
            padding: 40px;
            border-radius: 22px;
            text-align: center;
            font-size: 22px;
            box-shadow: 0 10px 30px #00000015;
        }

        @media (max-width: 600px) {
            .admin-header h1 {
                font-size: 28px;
            }

            .order-card {
                padding: 18px;
            }

            .order-number {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="admin-header">
        <h1>لوحة إدارة الطلبات</h1>
        <p>هنا يمكنك متابعة جميع الطلبات التي تم إرسالها من صفحة السلة</p>
    </div>

    <div class="admin-container">

        <?php if ($result->num_rows > 0): ?>

            <?php while ($order = $result->fetch_assoc()): ?>

                <div class="order-card">

                    <div class="order-top">
                        <div class="order-number">
                            طلب رقم #<?php echo $order["order_id"]; ?>
                        </div>

                        <div class="order-status">
                            <?php echo $order["status"]; ?>
                        </div>
                    </div>

                    <div class="order-info">
                        <div class="info-box">
                            <strong>اسم العميل</strong>
                            <?php echo $order["name"]; ?>
                        </div>

                        <div class="info-box">
                            <strong>رقم الجوال</strong>
                            <?php echo $order["phone"]; ?>
                        </div>

                        <div class="info-box">
                            <strong>المدينة</strong>
                            <?php echo $order["city"]; ?>
                        </div>

                        <div class="info-box">
                            <strong>تاريخ الطلب</strong>
                            <?php echo $order["created_at"]; ?>
                        </div>

                        <div class="info-box">
                            <strong>العنوان</strong>
                            <?php echo $order["address"]; ?>
                        </div>

                        <div class="info-box">
                            <strong>ملاحظات العميل</strong>
                            <?php echo $order["notes"] ? $order["notes"] : "لا توجد ملاحظات"; ?>
                        </div>
                    </div>

                    <div class="products-box">
                        <strong>المنتجات المطلوبة:</strong>
                        <br>
                        <?php echo nl2br($order["order_products"]); ?>
                    </div>

                    <div class="total">
                        الإجمالي: <?php echo $order["total_price"]; ?> ر.س
                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty">
                لا توجد طلبات حتى الآن
            </div>

        <?php endif; ?>

    </div>

</body>
</html>