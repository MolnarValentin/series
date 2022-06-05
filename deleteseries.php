<?php
include('seriesstorage.php');

session_start();

$isadmin = false;
$loggedin = false;

if (isset($_SESSION['user']) || isset($_SESSION['isadmin']) || isset($_SESSION['loggedin'])) {
    $user = $_SESSION['user'];
    $isadmin = $_SESSION['isadmin'];
    $loggedin = $_SESSION['loggedin'];
    if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] === false) {
        header('Location: index.php');
        exit();
    }
}

$seriesStorage = new SeriesStorage();

if (!isset($_GET['id'])) {
    header('Location: addseries.php');
    exit();
}

$id = $_GET['id'];

$data = [];
$errors = [];


$series = new SeriesStorage();

if (!$series) {
    $errors['global'] = 'Nem létező id!';
}

$seriesStorage->delete($id);
header('Location: index.php');

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