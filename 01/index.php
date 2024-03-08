<?php

$dsn = "mysql:host=localhost;dbname=test;charset=utf8";
$opt = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$db = new PDO($dsn, 'root', 'root', $opt);

function dump($data)
{
    echo '<pre>' . print_r($data, 1) . '</pre>';
}

$fillable = ['name', 'email', 'password'];

function load($fields)
{
    $data = [];
    foreach ($_POST as $k => $v) {
        if (in_array($k, $fields)) {
            $data[$k] = $v;
        }
    }
    return $data;
}

function save($tbl, $data = [])
{
    global $db;
    // insert into tbl (`name`, `email`, `password`) values (:name, :email, :password)
    // fields
    $fields_keys = array_keys($data);
    $fields = array_map(fn($field) => "`{$field}`", $fields_keys);
    $fields = "(" . implode(',', $fields) . ")";

    // values
    $values_placehodels = array_map(fn($v) => ":{$v}", $fields_keys);
    $values_placehodels = "(" . implode(',', $values_placehodels) . ")";
    $query = "insert into {$tbl} {$fields} values {$values_placehodels}";
    $stmt = $db->prepare($query);
    $stmt->execute($data);
    return $db->lastInsertId();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = load($fillable);
    var_dump(save('register', $data));
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-3">
    <div class="row">
        <div class="col-md-6 offset-md-3">

            <form action="" method="post">

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input name="name" type="text" class="form-control" id="name" placeholder="Name">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input name="email" type="text" class="form-control" id="email" placeholder="Email">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" id="password" placeholder="Password">
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>