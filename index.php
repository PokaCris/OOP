<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <title>OOP</title>
</head>

<body>
    <div class="container">

        <?php
        session_start();

        class Product
        {
            public $name;
            public $price;

            public function __construct($name, $price)
            {
                $this->name = $name;
                $this->price = $price;
            }

            public function getProduct()
            {
                return "(Name: {$this->name}, Price: {$this->price})";
            }

            public static function searchByName($products, $searchName)
            {
                foreach ($products as $key => $product) {
                    if ($product->name === $searchName) {
                        return $product;
                    }
                }
                return null;
            }
        }

        if (!isset($_SESSION['products'])) {
            $_SESSION['products'] = [
                new Product("Laptop", 80000),
                new Product("Tablet", 35000),
                new Product("Headphones", 10000)
            ];
            $_SESSION['products_created'] = true;
        }

        $products = &$_SESSION['products'];

        if (isset($_POST["add"])) {
            $name = $_POST["name"];
            $price = $_POST["price"];

            $products[] = new Product($name, $price);

            // чтобы при перезагрузке страницы продукт не добавлялся повторно
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        if (isset($_POST["search"])) {
            $searchName = $_POST["search_name"];
            $foundProduct = Product::searchByName($products, $searchName);
        }

        class Category
        {
            public $name;
            public $list_products;

            public function __construct($name, $list_products)
            {
                $this->name = $name;
                $this->list_products = $list_products;
            }

            public function getCategoryName()
            {
                return $this->name;
            }

            public function getCategoryProducts()
            {
                return $this->list_products;
            }

            public static function searchByName($categories, $searchName)
            {
                foreach ($categories as $category) {
                    if ($category->name === $searchName) {
                        return $category;
                    }
                }
                return null;
            }
        }

        if (!isset($_SESSION['categories'])) {
            $_SESSION['categories'] = [];
        }

        $categories = &$_SESSION['categories'];

        if (isset($_POST['add_category'])) {
            $categoryName = $_POST['category_name'];
            $existingCategory = null;

            foreach ($categories as $category) {
                if ($category->name === $categoryName) {
                    $existingCategory = $category;
                    break;
                }
            }

            if ($existingCategory !== null) {
                foreach ($products as $product) {
                    $existingCategory->list_products[] = $product;
                }
            } else {
                $newCategory = new Category($categoryName, $products);
                $categories[] = $newCategory;
            }

            $products = [];
            $_SESSION['products'] = [];
        }

        if (isset($_POST["clear_session"])) {
            session_destroy();
            session_start();
        }

        $selectedCategory = null;
        if (isset($_GET['category'])) {
            $categoryIndex = $_GET['category'];
            if (isset($categories[$categoryIndex])) {
                $selectedCategory = $categories[$categoryIndex];
            }
        }

        ?>

        <h2 class="my-4 text-primary">Add new product</h2>

        <div class="row border border-primary rounded p-4">
            <form method="POST" class="col-md-10 d-flex flex-column">
                <div class="mb-2">
                    <label for="name" style="width: 200px;">Product name</label>
                    <input type="text" name="name" placeholder="Enter name">
                </div>
                <div class="mb-2">
                    <label for="price" style="width: 200px;">Product price</label>
                    <input type="text" name="price" placeholder="Enter price">
                </div>
                <button type="submit" name="add" class="col-3 mt-3 btn btn-sm btn-primary">Add</button>
            </form>
        </div>

        <div class="mt-4">
            <h4 class="mb-4">Product list:</h4>
            <?php if (empty($products)) : ?>
                <div class="text-muted">Empty list</div>
            <?php else : ?>
                <?php foreach ($products as $product) : ?>
                    <div class="border border-primary rounded p-2 mb-2">
                        <?= $product->getProduct() ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <h2 class="my-4 text-success">Found product</h2>

        <div class="row my-4 border border-success rounded p-4">
            <form method="POST" class="col-md-10 d-flex flex-column">
                <div class="mb-2">
                    <label for="search_name" style="width:200px;">Search product by name</label>
                    <input type="text" name="search_name" placeholder="Enter product name">
                </div>
                <button type="submit" name="search" class="col-3 mt-3 btn btn-sm btn-success">Search</button>
            </form>
        </div>

        <?php if (isset($_POST["search"])) : ?>
            <div class="my-4">
                <h4 class="mb-4">Search result:</h4>
                <?php if ($foundProduct !== null) : ?>
                    <div class="border border-success rounded p-2 mb-2">
                        <?= $foundProduct->getProduct() ?>
                    </div>
                <?php else : ?>
                    <div class="text-danger border rounded p-2">
                        Product "<?= $searchName ?>" not found
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <h2 class="my-4 text-info">Categories</h2>

        <div class="row my-4 border border-info rounded p-4">
            <form method="POST" class="col-md-10 d-flex flex-column">
                <div class="mb-2">
                    <label for="category_name" style="width: 200px;">Category name</label>
                    <input type="text" name="category_name" placeholder="Enter category name">
                </div>
                <button type="submit" name="add_category" class="col-3 mt-3 btn btn-sm btn-info btn-secondary">Add category</button>
            </form>
        </div>

        <?php if (isset($_POST["add_category"])) : ?>
            <div class="my-4">
                <h4 class="mb-4">Category list:</h4>
                <?php foreach ($categories as $category) : ?>
                    <div class="border border-info rounded p-3 mb-3">
                        <h5><?= $category->getCategoryName() ?></h5>
                        <?php foreach ($category->getCategoryProducts() as $product) : ?>
                            <div class="p-2 mb-2 ms-3">
                                <?= $product->getProduct() ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <h4 class="mb-4">All categories:</h4>
            <?php if (empty($categories)) : ?>
                <div class="text-muted">Empty list</div>
            <?php else : ?>
                <div class="list-group">
                    <?php foreach ($categories as $index => $category) : ?>
                        <a href="?category=<?= $index ?>" class="list-group-item list-group-item-action">
                            <?= $category->getCategoryName() ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($selectedCategory !== null) : ?>
            <div class="mt-4">
                <h4 class="mb-4">Products in category "<?= $selectedCategory->getCategoryName() ?>":</h4>
                <?php foreach ($selectedCategory->getCategoryProducts() as $product) : ?>
                    <div class="border border-warning rounded p-2 mb-2">
                        <?= $product->getProduct() ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row my-4">
            <form method="POST" class="col-md-10 d-flex flex-column">
                <button type="submit" name="clear_session" class="col-3 mt-3 btn btn-danger">Clear Session</button>
            </form>
        </div>

    </div>
</body>

</html>