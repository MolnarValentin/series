<?php
include('seriesstorage.php');

session_start();

$isadmin = false;
$loggedin = false;

if (isset($_SESSION['user']) || isset($_SESSION['isadmin']) || isset($_SESSION['loggedin'])) {
    $user = $_SESSION['user'];
    $isadmin = $_SESSION['isadmin'];
    $loggedin = $_SESSION['loggedin'];
    if(isset($_SESSION['isadmin']) && $_SESSION['isadmin'] === false) {
        header('Location: index.php');
        exit();
    }
}

if (!isset($_GET['id'])) {
    header('Location: addseries.php');
    exit();
}


$id = $_GET['id'];

$seriesstorage = new SeriesStorage();

$series = $seriesstorage->findById($id);

$data = [];
$errors = [];

if (!$series) {
    $errors['global'] = 'Nem létező id!';
}

function validate($post, &$data, &$errors)
{



    if (!isset($post['title'])) {
        $errors['title'] = 'A sorozat nevének megadása kötelező!';
    } else if (trim($post['title']) === '') {
        $errors['title'] = 'A sorozat nevének megadása kötelező!';
    } else {
        $data['title'] = $post['title'];
    }


    if (!isset($post['year'])) {
        $errors['year'] = 'Az év megadása kötelező!';
    } else if (trim($post['year']) === '') {
        $errors['year'] = 'Az év megadása kötelező!';
    } else if (!filter_var((int)$post['year'], FILTER_VALIDATE_INT)) {
        $errors['year'] = 'Nem megfelelő dátum formátum!';
    } else {
        $data['year'] = (int)$post['year'];
        if ($data['year'] < 1900 || $data['year'] > idate("Y")) {
            $errors['year'] = 'Dátum nem lehet kissebb 1900-nál, vagy nagyobb a mai dátumnál!';
        }
    }


    if (!isset($post['plot'])) {
        $errors['plot'] = 'Összefoglaló megadása kötelező!';
    } else if (trim($post['plot']) === '') {
        $errors['plot'] = 'Összefoglaló megadása kötelező!';
    } else {
        $data['plot'] = $post['plot'];
    }



    if (!isset($post['cover'])) {
        $errors['cover'] = 'A jelszó megadása kötelező!';
    } else if (trim($post['cover']) === '') {
        $errors['cover'] = 'A jelszó megadása kötelező!';
    } else if (!filter_var($post['cover'], FILTER_VALIDATE_URL)) {
        $errors['cover'] = 'Nem megfelelő URL dátum formátum!';
    } else {
        $data['cover'] = $post['cover'];
    }

    if (!isset($post['episodes'])) {
        $errors['episodes'] = 'Az epizódok számának megadása kötelező!';
    } else if (trim($post['episodes']) === '') {
        $errors['episodes'] = 'Az epizódok számának megadása kötelező!';
    } else if (!filter_var((int)$post['episodes'], FILTER_VALIDATE_INT)) {
        $errors['episodes'] = 'Nem megfelelő szám formátum!';
    } else {
        $data['episodes'] = (int)$post['episodes'];
    }



    return count($errors) === 0;
}


//foprogram



if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {

        $data['episodes'] = array();

        for ($i = 1; $i <= count($series['episodes']); $i++) {
            $data['episodes'][$i]['id'] = '';
            $data['episodes'][$i]['date'] = '';
            $data['episodes'][$i]['title'] = '';
            $data['episodes'][$i]['plot'] = '';
            $data['episodes'][$i]['rating'] = '';
        }

        for ($i = 1; $i <= count($series['episodes']); $i++) {
            $data['episodes'][$i]['id'] = $series['episodes'][$i]['id'];
            $data['episodes'][$i]['date'] = $series['episodes'][$i]['date'];
            $data['episodes'][$i]['title'] = $series['episodes'][$i]['title'];
            $data['episodes'][$i]['plot'] = $series['episodes'][$i]['plot'];
            $data['episodes'][$i]['rating'] = $series['episodes'][$i]['rating'];
        }

        $data['id'] = $series['id'];

        $seriesstorage->update($id, $data);

        header('location: index.php');
        exit();
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <title>Sorozat Módosítása</title>
    <link rel="icon" type='image/x-icon' href="images/Fasticon-Leopard-Iphone-Movie-Folder.ico">
</head>

<body>

    <div class="container-fluid">
        <div class="row mt-5">
            <?php if (isset($errors['global'])) : ?>
                <div class="row">
                    <div class="col text-center">
                        <?= $errors['global'] ?>
                    </div>
                </div>
            <?php endif ?>
            <form class="form-horizontal" method="post" action="" novalidate>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput" id="formGroupExampleInput">Sorozat címe:</label>
                    <input type="text" class="form-control addseriesname  <?php if (isset($errors['title'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput" placeholder="Sorozat címe" name="title" value="<?= $_POST['title'] ?? $series['title'] ?? '' ?>">
                    <div id="formGroupExampleInput" class="invalid-feedback">
                        <?php if (isset($errors['title'])) : ?>
                            <?= $errors['title'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput2" id="formGroupExampleInput2" class="formcim">Megjelenés dátuma:</label>
                    <input type="number" min="1900" max="<?= idate("Y") ?>" id="formGroupExampleInput2" class="form-control addseriesyear <?php if (isset($errors['year'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput2" placeholder="2022" name="year" value="<?= $_POST['year'] ?? $series['year'] ?? '' ?>">
                    <div id="formGroupExampleInput2" class="invalid-feedback">
                        <?php if (isset($errors['year'])) : ?>
                            <?= $errors['year'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput3" id="formGroupExampleInput3" class="formcim">Összefoglaló</label>
                    <input class="form-control addseriesplot <?php if (isset($errors['addseriesplot'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput3" placeholder="Összefoglaló" name="plot" value="<?= $_POST['plot'] ?? $series['plot'] ?? '' ?>">
                    <div id="formGroupExampleInput3" class="invalid-feedback">
                        <?php if (isset($errors['plot'])) : ?>
                            <?= $errors['plot'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput4" id="formGroupExampleInput4" class="formcim">Borítókép-link:</label>
                    <input type="text" class="form-control addcover <?php if (isset($errors['cover'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput4" placeholder="https://" name="cover" value="<?= $_POST['cover'] ?? $series['cover'] ?? '' ?>">
                    <div id="formGroupExampleInput4" class="invalid-feedback">
                        <?php if (isset($errors['cover'])) : ?>
                            <?= $errors['cover'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput5" id="formGroupExampleInput5" class="formcim">Epizódok száma:</label>
                    <input type="number" min="0" class="form-control addseriesepisodes <?php if (isset($errors['episodes'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput5" placeholder="0" name="episodes" value="<?= $_POST['episodes'] ?? count($series['episodes']) ?? '' ?>">
                    <div id="formGroupExampleInput5" class="invalid-feedback">
                        <?php if (isset($errors['episodes'])) : ?>
                            <?= $errors['episodes'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col text-center">
                            <button class="btn btn-outline-dark btn-sm regbtn" style="border-radius: 0; border-width:2px; margin-top:2.5rem">Módosítás</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="container">
                <div class="row">
                    <div class="col text-center">
                        <a class="btn btn-outline-dark btn-sm regbtn" style="border-radius: 0; border-width:2px; margin-top:0.5rem" href="index.php">Vissza/mégse</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>