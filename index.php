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


$seriesstorage = new SeriesStorage();

$series = $seriesstorage->findAll();


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
    <title>Főoldal</title>
    <link rel="icon" type='image/x-icon' href="images/Fasticon-Leopard-Iphone-Movie-Folder.ico">
</head>

<body>



    <div class="container-fluid">
        <div class="row">
            <?php include("menu.php"); ?>
        </div>

        <div class="row">
            <div class="container-fluid">
                <div class="row">
                    <p class="h2">Üdvözlet az oldalon!</p>
                </div>
                <div class="row col-lg-6 intro">
                    <p>
                        Itt sorozatok tárházáról kapsz ismertetést, többek között, hogy hány részes, mikor jelent meg, valamint a részek átfogó leírásáról kapsz betekintést.
                        Ha regisztrát felhasználó vagy el is mentheted az általad megtekintett filmeket, valamint, hogy melyik részeket láttad.
                    </p>
                </div>
            </div>

        </div>

        <div class="row mt-5">
            <h2>Összes sorozat</h2>
            <div class="table-responsive-xl">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-secondary">
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Cím</th>
                            <th scope="col">Részletek</th>
                            <th scope="col">Legutolsó rész megjelenése</th>
                            <th scope="col">Epizódok száma</th>
                            <th scope="col" <?php if (!$isadmin) : ?> class="d-none" <?php endif ?>>Sorozat szerkesztése</th>
                            <th scope="col" <?php if (!$isadmin) : ?> class="d-none" <?php endif ?>>Sorozat törlése</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 0 ?>
                        <?php foreach ($series as $key => $s) : ?>
                            <?php $dates = array() ?>
                            <?php foreach ($s['episodes'] as $ep) : ?>
                                <?php array_push($dates, $ep['date'])  ?>
                            <?php endforeach ?>
                            <?php $max = max(array_map('strtotime', $dates)); ?>
                            <tr>
                                <th scope="row"><?= ++$i ?></th>
                                <td><a href="reszletezooldal.php?id=<?= $s['id'] ?>"><?= $s['title'] ?></a></td>
                                <td><?= $s['plot'] ?></td>
                                <td class="text-nowrap"><?= date('Y-m-d', $max) ?></td>
                                <td><?= count($s['episodes']) ?></td>
                                <td <?php if (!$isadmin) : ?> class="d-none" <?php endif ?>>
                                    <form action="modifyseries.php?id=<?= $s['id'] ?>" method="post" novalidate class="m-auto">
                                        <button class="btn btn-outline-dark btn-sm regbtn m-auto" style="border-radius: 0; border-width:2px;">Szerkesztés</button>
                                    </form>
                                </td>
                                <td <?php if (!$isadmin) : ?> class="d-none" <?php endif ?>>
                                    <form action="deleteseries.php?id=<?= $s['id'] ?>" method="post" novalidate class="m-auto">
                                        <button class="btn btn-outline-dark btn-sm regbtn m-auto" type="submit" style="border-radius: 0; border-width:2px;">Törlés</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>

                </table>
            </div>
        </div>

        <div class="row mt-5" <?php if ($isadmin === false && $loggedin === false) : ?> style="display: none;" <?php endif ?>>
            <h2>Elkezdett sorozatok</h2>
            <div class="table-responsive-xl">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-secondary">
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Cím</th>
                            <th scope="col">Részletek</th>
                            <th scope="col">Legutolsó rész megjelenése</th>
                            <th scope="col" <?php if (!$isadmin) : ?> style="display: none;" <?php endif ?>>Sorozat szerkesztése</th>
                            <th scope="col" <?php if (!$isadmin) : ?> style="display: none;" <?php endif ?>>Sorozat törlése</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 0 ?>
                        <?php foreach ($series as $s) : ?>
                            <?php if ($loggedin && isset($user['watched']) && in_array($s['id'], array_keys($user['watched']))) : ?>
                                <?php $dates = array() ?>
                                <?php foreach ($s['episodes'] as $ep) : ?>
                                    <?php array_push($dates, $ep['date'])  ?>
                                <?php endforeach ?>
                                <?php $max = max(array_map('strtotime', $dates)); ?>
                                <tr>
                                    <th scope="row"><?= ++$i ?></th>
                                    <td><a href="reszletezooldal.php?id=<?= $s['id'] ?>"><?= $s['title'] ?></a></td>
                                    <td><?= $s['plot'] ?></td>
                                    <td><?= date('Y-m-d', $max) ?></td>
                                    <td <?php if ($isadmin === false) : ?> style="display: none;" <?php endif ?>>
                                        <form action="modifyseries.php?id=<?= $s['id'] ?>" method="post" novalidate class="m-auto">
                                            <button class="btn btn-outline-dark btn-sm regbtn m-auto" style="border-radius: 0; border-width:2px;">Szerkesztés</button>
                                        </form>
                                    </td>
                                    <td <?php if ($isadmin === false) : ?> style="display: none;" <?php endif ?>>
                                        <form action="deleteseries.php?id=<?= $s['id'] ?>" method="post" novalidate class="m-auto">
                                            <button class="btn btn-outline-dark btn-sm regbtn m-auto" type="submit" style="border-radius: 0; border-width:2px;">Törlés</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endif ?>
                        <?php endforeach ?>
                    </tbody>

                </table>
            </div>
        </div>


        <a <?php if ($isadmin === false) : ?> style="display: none;" <?php endif ?> href="addseries.php" class="btn basicbutton">Sorozat hozzáadása</a>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>