<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mrówczarnia - Strona Główna</title>
  <link rel="icon" type="image/x-icon" href="favicon.png">
<link rel="stylesheet" href="style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


<?php
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'Formutor');
    define('DB_PASSWORD', 'Cr7OHKpuVpkzWaR');
    define('DB_NAME', 'formutor');

    function sortProducts($products) {
        usort($products, function($a, $b) {
            $priority = [
                'purple-tag' => 1,
                'black-tag' => 2,
                'red-tag' => 3
            ];

            $aQuantity = $a['quantity'];
            $bQuantity = $b['quantity'];

            $aClass = ($aQuantity > 1) ? 'black-tag' : (($aQuantity == 1) ? 'purple-tag' : 'red-tag');
            $bClass = ($bQuantity > 1) ? 'black-tag' : (($bQuantity == 1) ? 'purple-tag' : 'red-tag');

            return $priority[$aClass] - $priority[$bClass];
        });
        return $products;
    }

    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($link === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }

    $products = array();

    $sql = "SELECT id, name, photo, price, description, sale_price, quantity FROM produkty";

    if ($result = mysqli_query($link, $sql)) {
        // Fetch products from result set
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }

        $products = sortProducts($products);

        mysqli_free_result($result);
    } else {
        echo "Error: " . mysqli_error($link);
    }

    mysqli_close($link);
?>

</head>
<body>

<div class="header">
  <a href="home.html"><img src="mrowkaTitle.png" alt="Home"></a>
  <h1 class="shop-name">Mrówczarnia</h1>
  <div style="flex-grow: 1;"></div>
</div>

    <div class="navbar">
        <a href="index.php">Strona Główna<img src="anticon.png" alt="Home"></a>
        <a href="koszyk.php">Koszyk <img src="koszyk.png" alt="Koszyk"></a>
        <a href="profil.php">Konto <img src="anthead.png" alt="Konto"></a>
    </div>


<div class="search-bar">
  <input style="margin-top:5px" type="text" id="searchInput" onkeyup="filterAnts()" placeholder="Szukaj produktu...">
</div>

 <?php
                                    session_start();

                                    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                                        $user_role = $_SESSION["role"];

    if ($user_role === "admin") {
echo '
<h5>
 
           
<h5>
<div class="admin-panel">
    <h2>Admin Panel</h2>
<form id="addProductForm" enctype="multipart/form-data">
<label style="color:#b58adb;" for="name">Nazwa:</label>
<input type="text" name="name" id="name"><br><br>

                                    <label style="color:#b58adb;" for="photo">Zdjęcie:</label>
                                <input type="file" name="photo" id="photo"><br><br>


                                <label style="color:#b58adb;" for="description">Opis:</label>
                                <input type="text" name="description" id="description"><br><br>

                                <label style="color:#b58adb;" for="price">Cena:</label>
                                <input type="text" name="price" id="price"><br><br>

                                <label style="color:#b58adb;" for="sale_price">Cena (promocja):</label>
                                <input type="text" name="sale_price" id="sale_price"><br><br>

<label style="color:#b58adb;" for="quantity">Ilość:</label>
<input type="number" name="quantity" id="quantity" min="0" value="0"><br><br>


                                <input class="button"  type="submit" value="Dodaj produkt">
                            </form>
    </h5>
                            </div>
                        </div>
  </div>

    </h5>';
                                    

    }
}

?>

 
  </div>
</div>
<div class="main-content">
  <div class="item-list" id="antList">
<?php foreach ($products as $product): ?>
    <?php
        // Determine the tag class and text based on quantity
        $tagClass = '';
        $tagText = '';
        if ($product['quantity'] > 1) {
            $tagClass = 'black-tag';
        } elseif ($product['quantity'] == 1) {
            $tagClass = 'purple-tag';
            $tagText = '<div class="tag">ostatnia sztuka!</div>';
        } elseif ($product['quantity'] == 0) {
            $tagClass = 'red-tag';
            $tagText = '<div class="tag">tymczasowo niedostępne</div>';
        }
    ?>
      <a href="product_details.php?id=<?= $product['id'] ?>" class="item <?= $tagClass ?>">
          <div class="item-name"><?= htmlspecialchars($product['name']) ?></div>
          <?php if (!empty($product['photo'])): ?>
              <img src="data:image/jpeg;base64,<?= base64_encode($product['photo']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
          <?php else: ?>
              <img src="default-image.jpg" alt="No image">
          <?php endif; ?>
          <?= $tagText ?>
                <!-- Admin delete button -->
<?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["role"] === "admin"): ?>
    <form id="deleteProductForm<?= $product['id'] ?>" style="position: absolute; top: 0; right: 0;">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <button class="delete-btn" type="button" onclick="deleteProduct(<?= $product['id'] ?>);" title="Delete">X
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>
    </form>
<?php endif; ?>

                </a>
            <?php endforeach; ?>
        </div>
    </div>

<script src="index.js"></script>

</script>
<script>
$(document).ready(function() {
    $('#addProductForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: 'http://mrówczarnia.cba.pl/product_api_crud.php',  
        type: 'POST',
        headers: {
             'X-API-Key': 'TestApiKey', 
        },
        data: formData,
        contentType: false,  // Necessary for sending files
        processData: false,  // Necessary for sending files
        success: function(response) {
            alert('Product added successfully!');
                location.reload();
        },
        error: function(xhr, status, error) {
            alert('Failed to add product: ' + xhr.responseText);
            console.error("Error: " + error);
        }
    });
});

});
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        $.ajax({
            url: 'http://mrówczarnia.cba.pl/product_api_crud.php?id=' + productId,
            type: 'DELETE',
            headers: {
                'X-API-Key': 'TestApiKey',
            },
            success: function(response) {
                alert('Product deleted successfully!');
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('Failed to delete product: ' + xhr.responseText);
                console.error("Error: " + error);
            }
        });
    }
}


</script>

</body>
</html>			