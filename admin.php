<?php
require 'db.php';
session_start();

// ПРОВЕРКА: Если в сессии нет флага админа, выгоняем на страницу входа
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// --- ОБРАБОТКА ФОРМ ---

// Добавление нового товара
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $image = $_POST['image'] ?: 'econom.png';
    
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $category_id, $image]);
    $success_message = "Товар успешно добавлен!";
}

// Обновление товара
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'] ?: null;
    $image = $_POST['image'] ?: 'econom.png';
    
    $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, category_id=?, image=? WHERE id=?");
    $stmt->execute([$name, $description, $price, $category_id, $image, $id]);
    $success_message = "Товар успешно обновлен!";
    // Перенаправляем на вкладку товаров после обновления
    header("Location: admin.php?tab=products");
    exit();
}

// Удаление товара
if (isset($_GET['delete_product'])) {
    $id = $_GET['delete_product'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $success_message = "Товар удален!";
}

// Обновление статуса заказа
if (isset($_POST['update_order_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    $success_message = "Статус заказа обновлен!";
}

// Удаление заказа
if (isset($_GET['delete_order'])) {
    $id = $_GET['delete_order'];
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$id]);
    $success_message = "Заказ удален!";
}

// --- ПОЛУЧЕНИЕ ДАННЫХ ---

// Получаем категории
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Пагинация товаров
$products_limit = 5; // Товаров на странице
$products_page = isset($_GET['products_page']) ? (int)$_GET['products_page'] : 1;
$products_offset = ($products_page - 1) * $products_limit;

// Подсчет общего количества товаров
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_products_pages = ceil($total_products / $products_limit);

// Получаем товары с пагинацией
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $products_limit, PDO::PARAM_INT);
$stmt->bindValue(2, $products_offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// Получаем заказы
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();

// Товар для редактирования (если выбран)
$edit_product = null;
if (isset($_GET['id']) && isset($_GET['tab']) && $_GET['tab'] == 'edit') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_product = $stmt->fetch();
}

// Определяем активную вкладку
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'products';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора - Perfect Doors</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .admin-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .admin-header h1 { color: var(--primary-blue); }
        
        /* Вкладки */
        .tabs { display: flex; gap: 5px; margin-bottom: 20px; border-bottom: 2px solid #eee; }
        .tab-btn { 
            padding: 15px 25px; 
            background: #f5f5f5; 
            border: none; 
            cursor: pointer; 
            font-size: 16px; 
            border-radius: 5px 5px 0 0;
            transition: all 0.3s;
        }
        .tab-btn:hover { background: #e0e0e0; }
        .tab-btn.active { background: var(--primary-blue); color: white; }
        
        /* Содержимое вкладок */
        .tab-content { display: none; padding: 20px; background: #fff; border-radius: 0 0 5px 5px; }
        .tab-content.active { display: block; }
        
        /* Таблицы */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .data-table th { background: #f5f5f5; font-weight: 600; }
        .data-table tr:hover { background: #f9f9f9; }
        
        /* Статусы заказов */
        .status-new { color: #ff9800; font-weight: bold; }
        .status-in_progress { color: #2196F3; font-weight: bold; }
        .status-completed { color: #4CAF50; font-weight: bold; }
        .status-cancelled { color: #f44336; font-weight: bold; }
        
        /* Кнопки */
        .btn-edit { background: #2196F3; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-delete { background: #f44336; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-view { background: #4CAF50; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        
        /* Формы */
        .form-card { background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .form-card h3 { margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-group input, .form-group textarea, .form-group select { 
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 3px; 
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        /* Сообщения */
        .success-message { background: #4CAF50; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error-message { background: #f44336; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        
        /* Модальное окно */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 5% auto; padding: 20px; width: 90%; max-width: 600px; border-radius: 5px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-close { cursor: pointer; font-size: 24px; }
        
        .action-buttons { display: flex; gap: 5px; }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container" style="display:flex; justify-content: space-between; align-items: center;">
            <h1>Панель администратора</h1>
            <a href="logout.php" style="color: red; font-weight: bold;">Выйти</a>
        </div>
    </header>

    <main class="admin-container">
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>
        
        <!-- Вкладки -->
        <div class="tabs">
            <button class="tab-btn <?= $active_tab == 'products' ? 'active' : '' ?>" onclick="switchTab('products')">Товары</button>
            <button class="tab-btn <?= $active_tab == 'orders' ? 'active' : '' ?>" onclick="switchTab('orders')">Заказы</button>
            <button class="tab-btn <?= $active_tab == 'add' ? 'active' : '' ?>" onclick="switchTab('add')">Добавить товар</button>
        </div>
        
        <!-- Вкладка: Товары -->
        <div id="products" class="tab-content <?= $active_tab == 'products' ? 'active' : '' ?>">
            <h2>Управление товарами</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Цена</th>
                        <th>Изображение</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['category_name'] ?? 'Без категории') ?></td>
                        <td><?= number_format($product['price'], 0, '', ' ') ?> ₽</td>
                        <td><?= htmlspecialchars($product['image']) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="?tab=edit&id=<?= $product['id'] ?>" class="btn-edit">Редактировать</a>
                                <a href="?tab=products&delete_product=<?= $product['id'] ?>" class="btn-delete" onclick="return confirm('Вы уверены?')">Удалить</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Пагинация товаров -->
            <?php if ($total_products_pages > 1): ?>
            <div style="margin-top: 20px; text-align: center;">
                <?php for ($i = 1; $i <= $total_products_pages; $i++): ?>
                    <a href="?tab=products&products_page=<?= $i ?>" 
                       style="display: inline-block; padding: 8px 12px; margin: 0 3px; border: 1px solid #ddd; text-decoration: none; <?= $i == $products_page ? 'background: #479DCB; color: white;' : 'color: #333;' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Вкладка: Редактирование товара -->
        <?php if ($active_tab == 'edit' && $edit_product): ?>
        <div id="edit" class="tab-content active">
            <h2>Редактирование товара</h2>
            <div class="form-card">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Название товара:</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($edit_product['name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Категория:</label>
                            <select name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $edit_product['category_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Цена (руб.):</label>
                            <input type="number" name="price" value="<?= $edit_product['price'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Изображение (имя файла):</label>
                            <input type="text" name="image" value="<?= htmlspecialchars($edit_product['image']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Описание:</label>
                        <textarea name="description" rows="4"><?= htmlspecialchars($edit_product['description']) ?></textarea>
                    </div>
                    <button type="submit" name="update_product" class="btn">Сохранить изменения</button>
                    <a href="?tab=products" class="btn" style="background: #999;">Отмена</a>
                </form>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Вкладка: Заказы -->
        <div id="orders" class="tab-content <?= $active_tab == 'orders' ? 'active' : '' ?>">
            <h2>Управление заказами</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя клиента</th>
                        <th>Телефон</th>
                        <th>Дата замера</th>
                        <th>Статус</th>
                        <th>Дата создания</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['name']) ?></td>
                        <td><?= htmlspecialchars($order['phone']) ?></td>
                        <td><?= $order['order_date'] ? date('d.m.Y', strtotime($order['order_date'])) : '-' ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status" onchange="this.form.submit()" style="padding: 5px;">
                                    <option value="new" <?= $order['status'] == 'new' ? 'selected' : '' ?>>Новый</option>
                                    <option value="in_progress" <?= $order['status'] == 'in_progress' ? 'selected' : '' ?>>В работе</option>
                                    <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Выполнен</option>
                                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Отменен</option>
                                </select>
                                <input type="hidden" name="update_order_status" value="1">
                            </form>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                        <td>
                            <a href="?tab=orders&delete_order=<?= $order['id'] ?>" class="btn-delete" onclick="return confirm('Вы уверены?')">Удалить</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px;">Заказов пока нет</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Вкладка: Добавить товар -->
        <div id="add" class="tab-content <?= $active_tab == 'add' ? 'active' : '' ?>">
            <h2>Добавить новый товар</h2>
            <div class="form-card">
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Название товара:</label>
                            <input type="text" name="name" placeholder="Например: Эконом" required>
                        </div>
                        <div class="form-group">
                            <label>Категория:</label>
                            <select name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Цена (руб.):</label>
                            <input type="number" name="price" placeholder="Например: 15000" required>
                        </div>
                        <div class="form-group">
                            <label>Изображение (имя файла):</label>
                            <input type="text" name="image" placeholder="Например: econom.png">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Описание:</label>
                        <textarea name="description" rows="4" placeholder="Описание товара..."></textarea>
                    </div>
                    <button type="submit" name="add_product" class="btn">Добавить товар</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        function switchTab(tabId) {
            // Скрываем все вкладки
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            // Убираем активный класс со всех кнопок
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            // Показываем выбранную вкладку
            document.getElementById(tabId).classList.add('active');
            // Добавляем активный класс кнопке
            event.target.classList.add('active');
        }
    </script>
</body>
</html>

