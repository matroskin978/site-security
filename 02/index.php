<?php

session_start();

$dsn = "mysql:host=localhost;dbname=test;charset=utf8";
$opt = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$db = new PDO($dsn, 'root', 'root', $opt);

$stmt = $db->prepare("SELECT * FROM register");
$stmt->execute();
$users = $stmt->fetchAll();

function dump($data)
{
    echo '<pre>' . print_r($data, 1) . '</pre>';
}

$fillable = ['name', 'email', 'password'];
$required = [
    'name' => 'Name',
    'email' => 'Email',
    'password' => 'Password',
];

function load($fields)
{
    $data = [];
    foreach ($fields as $v) {
        $data[$v] = $_POST[$v] ?? '';
        /*if (in_array($k, $fields)) {
            $data[$k] = $v;
        }*/
    }
    return $data;
}

function validate($required, $data)
{
    $errors = '';
    foreach ($required as $k => $v) {
        if (empty($data[$k])) {
            $errors .= "<li>Field {$v} is required</li>";
        }
    }
    return $errors;
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

function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

function old($field_name)
{
    return isset($_SESSION['form_data'][$field_name]) ? h($_SESSION['form_data'][$field_name]) : '';
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = load($fillable);
    if ($errors = validate($required, $data)) {
        $_SESSION['errors'] = "<ul class='list-unstyled'>{$errors}</ul>";
        $_SESSION['form_data'] = $data;
    } else {
        $_SESSION['success'] = 'Success';
        save('register', $data);
    }
    header("Location: index.php");
    die;
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

            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['errors']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['errors']) ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']) ?>
            <?php endif; ?>

            <?= old('name'); ?>

            <form action="" method="post">

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input name="name" type="text" class="form-control" id="name" placeholder="Name"
                           value="<?= old('name'); ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input name="email" type="text" class="form-control" id="email" placeholder="Email"
                           value="<?= old('email'); ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" id="password" placeholder="Password"
                           value="<?= old('password'); ?>">
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>

            </form>

            <?php
            if (isset($_SESSION['form_data'])) {
                unset($_SESSION['form_data']);
            }
            ?>

            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <p><?= h($user['name']); ?> | <?= h($user['email']); ?></p>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>