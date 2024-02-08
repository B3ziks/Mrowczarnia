<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mrówczarnia - Szczegóły Produktu</title>
  <link rel="icon" type="image/x-icon" href="favicon.png">

<link rel="stylesheet" href="styledetails.css">

</head>
<body>
<div class="header">
  <a href="index.php"><img src="mrowkaTitle.png" alt="Home"></a>
  <h1 class="shop-name">Mrówczarnia</h1>
  <div style="flex-grow: 1;"></div>
</div>

    <div class="navbar">
        <a href="index.php">Strona Główna<img src="anticon.png" alt="Home"></a>
        <a href="koszyk.php">Koszyk <img src="koszyk.png" alt="Koszyk"></a>
        <a href="profil.php">Konto <img src="anthead.png" alt="Konto"></a>
    </div>

<?php
        session_start(); 

        // Database credentials
        define('DB_SERVER', 'localhost');
        define('DB_USERNAME', 'Admin');
        define('DB_PASSWORD', 'Cr7OHKpuVpkzWaR');
        define('DB_NAME', 'mrowczarnia');

        $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);

        $product_id = isset($_GET['id']) ? $_GET['id'] : 0;

        $stmt = $conn->prepare("SELECT * FROM produkty WHERE id = :id");
        $stmt->execute(['id' => $product_id]);
        $product = $stmt->fetch();

        $sql = "SELECT comments.content, comments.created_at, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE product_id = ? ORDER BY comments.created_at DESC";
        $comment_stmt = $conn->prepare($sql);
        $comment_stmt->execute([$product_id]);
        $comments = $comment_stmt->fetchAll();
    ?>


<div id="productDetails">
    <div class="product-image-container">
        <?php
            if ($product['photo']) {
                echo "<img src='data:image/jpeg;base64," . base64_encode($product['photo']) . "' alt='Product Image' />";
            } else {
                echo "<img src='default-image.jpg' alt='Default Image' />";
            }
        ?>
    </div>
<?php
session_start();

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'Formutor');
define('DB_PASSWORD', 'Cr7OHKpuVpkzWaR');
define('DB_NAME', 'formutor');

$conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);

$product_id = isset($_GET['id']) ? $_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM produkty WHERE id = :id");
$stmt->execute(['id' => $product_id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: programy.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addToBasket'])) {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if (!isset($_SESSION['basket'])) {
        $_SESSION['basket'] = array();
    }

    $productExists = false;
    foreach ($_SESSION['basket'] as $key => $item) {
        if ($item['id'] == $product_id) {
            $_SESSION['basket'][$key]['quantity'] += $quantity;
            $productExists = true;
            break;
        }
    }

    if (!$productExists) {
        $_SESSION['basket'][] = array(
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity
        );
    }

    header("Location: ".$_SERVER['PHP_SELF']."?id=".$product_id);
    exit();
}


?>

<div class="product-info">
    <h1><?= htmlspecialchars($product['name']) ?></h1>

    <!-- Edit Button for Admins -->
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["role"] === "admin"): ?>
        <a href="edit_product.php?id=<?= $product['id'] ?>" class="button edit-button">Edytuj</a>
    <?php endif; ?>
<br><br>
    <span>
        Cena: <?= $product['sale_price'] != 0.00 ? "<strike>" . $product['price'] . "</strike> " . $product['sale_price'] : $product['price'] ?> zł
    </span><br>
    <!-- Display remaining quantity -->
    <span>Pozostało: <?= $product['quantity'] ?> sztuk</span><br>
<?php if ($product['quantity'] <= 0): ?>
    <span style="color: red;">Produkt niedostępny</span><br>
<?php endif; ?>

 <form method="post" action="koszyk.php">
        <div class="quantity-controls">
            <label for="quantity">Ilość sztuk:</label>
            <button type="button" onclick="decreaseQuantity()">-</button>
            <input type="number" id="quantity" name="quantity" min="1" max="<?= $product['quantity'] ?>" value="1">
            <button type="button" onclick="increaseQuantity()">+</button>
        </div>
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <input type="hidden" name="name" value="<?= urlencode($product['name']) ?>">
        <input type="hidden" name="price" value="<?= urlencode($product['price']) ?>">
        <!-- Disable button if quantity is 0 -->
        <button type="submit" name="addToBasket" class="button" <?= $product['quantity'] <= 0 ? 'disabled' : '' ?>>Dodaj do koszyka</button>
    </form>
<br>
<p>Opis:</p>
<?php
$description = str_replace("\\n", "\n", $product['description']);
echo nl2br(htmlspecialchars($description));
?>

</div>

</div>


  </div>
</div>
   <div class="details-section">
        <div class="ratings-comments-container" style="margin-left: 20px;">
            <!-- Ratings and Comments Section -->
        </div>
    <!-- Display Comments -->
<div class="comments-header clearfix" style="margin-left: 20px;">
    <h2>Komentarze</h2>
</div>
<div class="comments-container clearfix" style="margin-left: 20px;">
        <!-- Comments Table -->
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Content</th>
                    <th>Date</th>
                    <th>Action</th> <!-- Add a header for the action column -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?= htmlspecialchars($comment['username']) ?></td>
                        <td><?= htmlspecialchars($comment['content']) ?></td>
                        <td><?= $comment['created_at'] ?></td>
                        <td>
                            <!-- Delete Comment Button -->
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <form method="post" action="delete_comment.php">
                                    <input type="hidden" name="comment_id" value="<?= $comment['id']; ?>">
                                    <button type="submit" onclick="return confirm('Are you sure?');">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($comments)): ?>
                    <tr><td colspan='4'>No comments yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div> <!-- End of comments container -->
<div class="add-comment-form clearfix">
   <!-- Add Comment Form -->
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
        <h1>Dodaj komentarz</h1> <!-- Dodaj komentarz header -->
        <form action="add_comment.php" method="post">
            <div class="form-group">
                <label>Treść:</label>
                <input class="form-control" type="text" maxlength="58" placeholder="wprowadź treść komentarza" name="content" required>
            </div>
            <!-- Hidden input for product_id -->
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
            
            <input class="btn" type="submit" value="Dodaj komentarz">
        </form>
    <?php else: ?>
        <!-- Display a message or a login link for non-logged-in users -->
        <p style="margin-left: 20px;">Aby dodać komentarz, <a href="login.php">zaloguj się</a>.</p>
    <?php endif; ?>
</div>
    <a href="index.php" class="button">&#x2B05; Powrót</a>

<script src="details.js"></script>

</body>
</html>