<?php

session_start();
include "database.php";

$account = null;

function e($value) {
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

function starts_good($value) {
    $value = trim($value);

    if ($value == "") {
        return false;
    }

    return preg_match("/^[\p{Arabic}a-zA-Z0-9]/u", $value);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_account"])) {
    if (!isset($_SESSION["user_id"])) {
        header("Location: Account.php?error=login_empty#login");
        exit;
    }

    $full_name = trim($_POST["full_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");

    if ($full_name == "" || $phone == "" || $address == "") {
        header("Location: Account.php?error=update_empty");
        exit;
    }

    if (!starts_good($full_name) || !starts_good($phone) || !starts_good($address)) {
        header("Location: Account.php?error=invalid_start");
        exit;
    }

    $user_id = $_SESSION["user_id"];

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $phone, $address, $user_id);
    $stmt->execute();

    $_SESSION["user_name"] = $full_name;

    header("Location: Account.php?success=update_success");
    exit;
}

if (isset($_SESSION["user_id"])) {
    $stmt = $conn->prepare("SELECT id, full_name, email, phone, address, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $account = $stmt->get_result()->fetch_assoc();
}

$messages = [
    "login_empty" => "الرجاء إدخال الإيميل وكلمة المرور",
    "login_wrong" => "الإيميل أو كلمة المرور غير صحيحة",
    "signup_empty" => "الرجاء تعبئة جميع بيانات التسجيل",
    "email_invalid" => "صيغة الإيميل غير صحيحة",
    "password_short" => "كلمة المرور يجب أن تكون 6 أحرف على الأقل",
    "email_exists" => "الإيميل موجود بالفعل",
    "signup_success" => "تم إنشاء الحساب بنجاح",
    "update_empty" => "الرجاء تعبئة جميع البيانات",
    "update_success" => "تم تحديث معلومات الحساب بنجاح",
    "invalid_start" => "لا يمكن أن تبدأ البيانات برمز"
];

$messageKey = $_GET["error"] ?? $_GET["success"] ?? "";
$message = $messages[$messageKey] ?? "";
$messageClass = isset($_GET["success"]) ? "success-message" : "error-message";

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Account</title>
    <link rel="stylesheet" href="Css/Common.css">
    <link rel="stylesheet" href="Css/Account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body>
    <section id="header">
        <div class="header-left">
            <img src="image/logo.png" alt="Logo">

            <div id="authLinks" class="auth-links">
                <a href="Account.php#signup">Sign Up</a>
                <a href="Account.php#login">Login</a>
            </div>
        </div>

        <div>
            <ul id="nav">
                <li><a href="HomePage.html">Home</a></li>
                <li><a href="ItemsPage.html">Items</a></li>
                <li><a href="AboutUsPage.html">About Us</a></li>
                <li><a href="ContactUsPage.html">Contact</a></li>
                <li>
                    <a href="Cart.html" class="cart-icon">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span id="cart-count" class="cart-count">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </section>

    <section class="auth-hero">
        <div class="auth-hero-text">
            <span>حسابي</span>
            <h1>سجّل دخولك أو أنشئ حساب جديد</h1>
            <p>من خلال الحساب تقدر تحفظ بياناتك وتكمل طلبك بسهولة.</p>
        </div>
    </section>

    <section class="auth-section">
        <?php if ($message != ""): ?>
            <div class="<?php echo $messageClass; ?>">
                <?php echo e($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($account): ?>
            <div class="account-card">
                <div class="account-icon">
                    <i class="fa-solid fa-user"></i>
                </div>

                <h2>معلومات الحساب</h2>

                <div class="account-info">
                    <div>
                        <strong>ID</strong>
                        <span><?php echo e($account["id"]); ?></span>
                    </div>

                    <div>
                        <strong>الاسم الكامل</strong>
                        <span><?php echo e($account["full_name"]); ?></span>
                    </div>

                    <div>
                        <strong>الإيميل</strong>
                        <span><?php echo e($account["email"]); ?></span>
                    </div>

                    <div>
                        <strong>رقم الجوال</strong>
                        <span><?php echo e($account["phone"]); ?></span>
                    </div>

                    <div>
                        <strong>العنوان</strong>
                        <span><?php echo e($account["address"]); ?></span>
                    </div>

                    <div>
                        <strong>تاريخ إنشاء الحساب</strong>
                        <span><?php echo e($account["created_at"]); ?></span>
                    </div>
                </div>

                <div class="edit-box">
                    <h3>تعديل معلومات الحساب</h3>

                    <form action="Account.php" method="POST">
                        <input type="hidden" name="update_account" value="1">

                        <input type="text" name="full_name" value="<?php echo e($account["full_name"]); ?>" required>
                        <input type="tel" name="phone" value="<?php echo e($account["phone"]); ?>" required>
                        <textarea name="address" required><?php echo e($account["address"]); ?></textarea>

                        <button type="submit">حفظ التعديلات</button>
                    </form>
                </div>

                <a class="logout-btn" href="Logout.php">تسجيل الخروج</a>
            </div>
        <?php else: ?>
            <div class="auth-container">
                <div class="auth-box" id="login">
                    <h2>Login</h2>
                    <p>إذا عندك حساب، سجّل دخولك من هنا.</p>

                    <form action="Login.php" method="POST">
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <button type="submit">Login</button>
                    </form>
                </div>

                <div class="auth-box" id="signup">
                    <h2>Sign Up</h2>
                    <p>إذا ما عندك حساب، أنشئ حساب جديد.</p>

                    <form action="signup.php" method="POST">
                        <input type="text" name="full_name" placeholder="Full Name" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="tel" name="phone" placeholder="Phone Number" required>
                        <textarea name="address" placeholder="Address" required></textarea>
                        <button type="submit">Sign Up</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </section>
    <script src="auth_nav.js"></script>
</body>

</html>