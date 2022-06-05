<?php

session_start();
include('seriesstorage.php');

$isadmin = false;
$loggedin = false;

if (isset($_SESSION['user']) || isset($_SESSION['isadmin']) || isset($_SESSION['loggedin'])) {
    $user = $_SESSION['user'];
    $isadmin = $_SESSION['isadmin'];
    $loggedin = $_SESSION['loggedin'];
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}


$id = $_GET['id'];

$seriesStorage = new SeriesStorage();

$series = $seriesStorage->findById($id);

$data = [];
$errors = [];

if (!$series) {
    $errors['global'] = 'Nem létező id!';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <title>Részletek</title>
    <link rel="icon" type='image/x-icon' href="images/Fasticon-Leopard-Iphone-Movie-Folder.ico">
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <?php include("menu.php"); ?>
        </div>
        <?php if (isset($errors['global'])) : ?>
            <div class="row">
                <div class="col text-center">
                    <?= $errors['global'] ?>
                </div>
            </div>
        <?php endif ?>
        <div class="row">
            <div class="col-lg-2">
                <p class="h3">Cím:</p>
                <p class="fs-4"><?= $series['title'] ?></p>
                <br>
                <p class="h5">Információ: </p>
                <br>
                <p><?= $series['plot'] ?></p>
                <br>
                <p class="h5">Epizódok száma:</p>
                <p><?= count($series['episodes']) ?></p>
                <p class="h5">Megjelenés: </p>
                <p><?= $series['year'] ?></p>
                <br>
                <p class="h5">Borítókép:</p>
                <img class="img-thumbnail mw-25" src="<?= $series['cover'] ?>" alt="">
            </div>
            <div class="col-lg-10">
                <p class="h5">Epizódok</p>


                <div class="table-responsive-xl">
                    <table class="table table-hover align-middle text-center w-auto">
                        <thead class="table-secondary container-fluid">
                            <tr class="">
                                <th scope="col"></th>
                                <th <?php if ($loggedin === false) : ?> style="display: none;" <?php endif ?> scope="col">Megnézve</th>
                                <th scope="col">Epizód címe</th>
                                <th scope="col">Közvetítve</th>
                                <th class="" scope="col">Leírás</th>
                                <th scope="col">Értékelés</th>
                                <th scope="col" <?php if ($isadmin === false) : ?> style="display: none;" <?php endif ?>>Epizód szerkesztése</th>
                                <th scope="col" <?php if ($isadmin === false) : ?> style="display: none;" <?php endif ?>>Epizód törlése</th>
                            </tr>
                </div>
                </thead>
                <tbody>
                    <?php $count = 0 ?>
                    <?php foreach ($series['episodes'] as $key => $ep) : ?>
                        <tr <?php if ($loggedin && isset($user['watched']) && in_array($series['id'], array_keys($user['watched'])) && ((int)$user['watched'][$id] >= (int)$key)) : ?> class="table-primary" <?php endif ?>>
                            <th scope="row"><?= ++$count ?></th>
                            <td <?php if ($loggedin === false) : ?> style="display: none;" <?php endif ?>>
                                <?php if (isset($user['watched']) && in_array($series['id'], array_keys($user['watched'])) && $user['watched'][$id] === $key) : ?>
                                    <form method="post" action="unseen.php?id=<?= $id ?>&eid=<?= $key ?>" novalidate>
                                        <button class="btn btn-outline-dark btn-sm regbtn m-auto text-nowrap" type="submit" value="submit" style="border-radius: 0; border-width:2px;">Nem Láttam</button>
                                    </form>
                                <?php elseif (!isset($user['watched']) || !in_array($series['id'], array_keys($user['watched'])) || $user['watched'][$id] + 1 === $key) : ?>
                                    <form method="post" action="seen.php?id=<?= $id ?>&eid=<?= $key ?>" novalidate>
                                        <button class="btn btn-outline-dark btn-sm regbtn m-auto text-nowrap" type="submit" value="submit" style="border-radius: 0; border-width:2px;">Láttam</button>
                                    </form>
                                <?php endif ?>
                            </td>

                            <td><?= $ep['title'] ?></td>
                            <td class="text-nowrap"><?= $ep['date'] ?></td>
                            <td><?= $ep['plot'] ?></td>
                            <td><?= $ep['rating'] ?></td>
                            <td <?php if ($isadmin === false) : ?> style="display: none;" <?php endif ?>>
                                <form action="modifyepisodes.php?sid=<?= $id ?>&eid=<?= $key ?>" method="post" novalidate class="m-auto">
                                    <button class="btn btn-outline-dark btn-sm regbtn m-auto" type="submit" style="border-radius: 0; border-width:2px;">Szerkesztés</button>
                                </form>
                            </td>
                            <td <?php if ($isadmin === false) : ?> style="display: none;" <?php endif ?>>
                                <form action="deleteepisodes.php?sid=<?= $id ?>&eid=<?= $key ?>" method="post" novalidate class="m-auto">
                                    <button class="btn btn-outline-dark btn-sm regbtn m-auto" type="submit" style="border-radius: 0; border-width:2px;">Törlés</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                </table>
            </div>


            <a <?php if ($isadmin === false) : ?> style="display: none;" <?php endif ?> href="addepisodes.php?id=<?= $id ?>" class="btn basicbutton">Epizód hozzáadása</a>


        </div>
    </div>
    <a style="display: inline-block;" href="index.php" class="btn basicbutton">Vissza</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>