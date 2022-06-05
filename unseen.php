<?php
session_start();
include('usersstorage.php');

$isadmin = false;
$loggedin = false;

if (isset($_SESSION['user']) || isset($_SESSION['isadmin']) || isset($_SESSION['loggedin'])) {
    $user = $_SESSION['user'];
    $isadmin = $_SESSION['isadmin'];
    $loggedin = $_SESSION['loggedin'];
}

if (!isset($_GET['eid'])) {
    header('Location: addseries.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: addseries.php');
    exit();
}

$id = $_GET['id'];
$id2 = $_GET['id'];
$key = $_GET['eid'];

if (!$_SESSION['user']) {
    $errors['global'] = 'Nem létező user!';
}

$usersstorage = new UsersStorage();

$newSeen = $user['watched'][$id] - 1;

$data['id'] = $user['id'];
$data['username'] = $user['username'];
$data['email'] = $user['email'];
$data['password'] = $user['password'];


$data['watched'] = $user['watched'];

$data['watched'][$id2] = $newSeen;

$id2 = $data['id'];


$usersstorage->update($id2, $data);

$_SESSION['user'] = $data;


header("location: reszletezooldal.php?id=$id");
exit();
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php if (isset($errors['global'])) : ?>
        <div class="row">
            <div class="col text-center">
                <?= $errors['global'] ?>
            </div>
        </div>
    <?php endif ?>
</body>

</html>