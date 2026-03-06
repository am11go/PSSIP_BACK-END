<?php
require 'db.php';

// --- Настройки пагинации ---
$limit = 6; // Кол-во товаров на странице
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// --- Сбор фильтров ---
$search = isset($_GET['search']) ? $_GET['search'] : '';
$cat_id = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';

// --- Построение SQL запроса ---
$sql = "SELECT * FROM products WHERE is_active = 1";
$params = [];

if ($search) {
    $sql .= " AND name LIKE ?";
    $params[] = "%$search%";
}
if ($cat_id) {
    $sql .= " AND category_id = ?";
    $params[] = $cat_id;
}

// Сортировка
if ($sort == 'price_asc') $sql .= " ORDER BY price ASC";
elseif ($sort == 'price_desc') $sql .= " ORDER BY price DESC";
else $sql .= " ORDER BY id DESC";

// Лимит для пагинации
$sql .= " LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Для подсчета общего числа страниц
$count_sql = "SELECT COUNT(*) FROM products WHERE is_active = 1";
if ($search) $count_sql .= " AND name LIKE '%$search%'";
if ($cat_id) $count_sql .= " AND category_id = $cat_id";

$total_products = $pdo->query($count_sql)->fetchColumn();
$total_pages = ceil($total_products / $limit);

// Получаем категории для фильтра
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог - Perfect Doors</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .filter-bar { background: #f4f4f4; padding: 20px; margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .pagination { margin-top: 30px; text-align: center; }
        .pagination a { padding: 10px; border: 1px solid #ccc; margin: 5px; text-decoration: none; color: #333; display: inline-block; }
        .pagination a.active { background: #479DCB; color: white; }
        .product-card { background: #F2F3F3; border-radius: 7px; padding: 20px; transition: border 0.3s; border: 2px solid transparent; min-height: 280px; position: relative; }
        .product-card:hover { border: 2px solid #479DCB; }
        .product-card h3 { font-size: 1.5em; margin-bottom: 10px; color: #121212; }
        .product-card .price { color: #479DCB; font-weight: bold; font-size: 1.2em; margin-bottom: 15px; }
        .product-card .description { font-size: 0.9em; color: #565F65; margin-bottom: 15px; }
        .product-card img { position: absolute; right: 10px; bottom: 10px; max-width: 45%; max-height: 45%; }
        .product-card .btn { position: absolute; bottom: 20px; left: 20px; }
        @media (max-width: 768px) {
            .product-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<header class="main-header">
    <div class="container">
        <h1>Каталог продукции</h1>
    </div>
</header>

<main class="container">
    <!-- Блок фильтрации и поиска -->
    <form class="filter-bar" method="GET">
        <input type="text" name="search" placeholder="Поиск..." value="<?= htmlspecialchars($search) ?>">
        
        <select name="category">
            <option value="">Все категории</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat_id == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="sort">
            <option value="id" <?= $sort == 'id' ? 'selected' : '' ?>>По умолчанию</option>
            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Дешевле</option>
            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Дороже</option>
        </select>
        <button type="submit" class="btn btn-small">Применить</button>
    </form>

    <!-- Вывод товаров -->
    <?php if (count($products) > 0): ?>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
        <div class="product-card">
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <p class="price"><?= number_format($p['price'], 0, '', ' ') ?> ₽</p>
            <p class="description"><?= htmlspecialchars($p['description'] ?? '') ?></p>
            <?php if ($p['image']): ?>
                <img src="assets/images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
            <?php endif; ?>
            <button class="btn btn-small open-popup">Заказать</button>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="text-align: center; padding: 40px;">Товары не найдены</p>
    <?php endif; ?>

    <!-- Пагинация -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $cat_id ?>&sort=<?= $sort ?>" 
               class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</main>

<!-- Подключаем popup для заказа -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('.open-popup').click(function(e) {
        e.preventDefault();
        $('#popup-form').css('display', 'block');
    });
    
    $('.popup-close').click(function() {
        $('#popup-form').css('display', 'none');
    });
    
    $(window).click(function(e) {
        if (e.target == $('#popup-form')[0]) {
            $('#popup-form').css('display', 'none');
        }
    });
});
</script>
</body>
</html>

