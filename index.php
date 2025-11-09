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
        }

        $products = &$_SESSION['products'];

        if (isset($_POST["add"])) {
            $name = $_POST["name"];
            $price = $_POST["price"];

            $products[] = new Product($name, $price);
        }

        if (isset($_POST["search"])) {
            $searchName = $_POST["search_name"];
            $foundProduct = Product::searchByName($products, $searchName);
        }

        ?>

        <h2 class="my-4">Add new product</h2>

        <div class="row border rounded p-4">
            <form method="POST" class="col-md-10 d-flex flex-column">
                <div class="mb-2">
                    <label for="name" style="width: 120px;">Product name</label>
                    <input type="text" name="name" placeholder="Enter name">
                </div>
                <div class="mb-2">
                    <label for="price" style="width: 120px;">Product price</label>
                    <input type="text" name="price" placeholder="Enter price">
                </div>
                <button type="submit" name="add" class="col-3 mt-3 btn btn-sm btn-secondary">Add</button>
            </form>
        </div>

        <?php if (isset($_POST["add"])) : ?>
            <div class="mt-4">
                <h3 class="mb-4">Product list:</h3>
                <?php foreach ($products as $product) : ?>
                    <div class="border rounded p-2 mb-2">
                        <?= $product->getProduct() ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2 class="my-4">Found product</h2>

        <div class="row my-4 border rounded p-4">
            <form method="POST" class="col-md-10 d-flex flex-column">
                <div class="mb-2">
                    <label for="searchName" style="width: 120px;">Search product by name</label>
                    <input type="text" name="search_name" placeholder="Enter product name">
                </div>
                <button type="submit" name="search" class="col-3 mt-3 btn btn-sm btn-secondary">Search</button>
            </form>
        </div>

        <?php if (isset($_POST["search"])) : ?>
            <div class="my-4">
                <h3 class="mb-4">Search result:</h3>
                <?php if ($foundProduct !== null) : ?>
                    <div class="border rounded p-2 mb-2 bg-light">
                        <?= $foundProduct->getProduct() ?>
                    </div>
                <?php else : ?>
                    <div class="text-danger">
                        Product "<?= $searchName ?>" not found
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</body>

</html>