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

$seriesstorage = new SeriesStorage();

function validate($post, &$data, &$errors)
{



    if (!isset($post['addseriesname'])) {
        $errors['addseriesname'] = 'A sorozat nevének megadása kötelező!';
    } else if (trim($post['addseriesname']) === '') {
        $errors['addseriesname'] = 'A sorozat nevének megadása kötelező!';
    } else {
        $data['addseriesname'] = $post['addseriesname'];
    }


    if (!isset($post['addseriesyear'])) {
        $errors['addseriesyear'] = 'Az év megadása kötelező!';
    } else if (trim($post['addseriesyear']) === '') {
        $errors['addseriesyear'] = 'Az év megadása kötelező!';
    } else if (!filter_var((int)$post['addseriesyear'], FILTER_VALIDATE_INT)) {
        $errors['addseriesyear'] = 'Nem megfelelő dátum formátum!';
    } else {
        $data['addseriesyear'] = (int)$post['addseriesyear'];
        if ($data['addseriesyear'] < 1900 || $data['addseriesyear'] > idate("Y")) {
            $errors['addseriesyear'] = 'Dátum nem lehet kissebb 1900-nál, vagy nagyobb a mai dátumnál!';
        }
    }


    if (!isset($post['addseriesplot'])) {
        $errors['addseriesplot'] = 'Összefoglaló megadása kötelező!';
    } else if (trim($post['addseriesplot']) === '') {
        $errors['addseriesplot'] = 'Összefoglaló megadása kötelező!';
    } else {
        $data['addseriesplot'] = $post['addseriesplot'];
    }



    if (!isset($post['addcover'])) {
        $errors['addcover'] = 'A jelszó megadása kötelező!';
    } else if (trim($post['addcover']) === '') {
        $errors['addcover'] = 'A jelszó megadása kötelező!';
    } else if (!filter_var($post['addcover'], FILTER_VALIDATE_URL)) {
        $errors['addcover'] = 'Nem megfelelő URL dátum formátum!';
    } else {
        $data['addcover'] = $post['addcover'];
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

$data = [];
$errors = [];


if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        //beolvasas
        $addseriesname = $data['addseriesname'];
        $addseriesyear = $data['addseriesyear'];
        $addseriesplot = $data['addseriesplot'];
        $addcover = $data['addcover'];

        unset($data['addseriesname']);
        unset($data['addseriesyear']);
        unset($data['addseriesplot']);
        unset($data['addcover']);
        
        
        $nbr = $data['episodes'];
        
        $data['episodes'] = array();
        for($i=1; $i<=$nbr; $i++) {
            $data['episodes'][$i]['id'] = '';
            $data['episodes'][$i]['date'] = '';
            $data['episodes'][$i]['title'] = '';
            $data['episodes'][$i]['plot'] = '';
            $data['episodes'][$i]['rating'] = '';
        }

        $addseriesepisodes = $data['episodes'];

        $seriesstorage->add([
            'year' => $addseriesyear,
            'title' => $addseriesname,
            'plot' => $addseriesplot,
            'cover' => $addcover,
            'episodes' => $addseriesepisodes
        ]);

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
    <title>Sorozat Hozzáadás</title>
    <link rel="icon" type='image/x-icon' href="images/Fasticon-Leopard-Iphone-Movie-Folder.ico">
</head>

<body>

    <div class="container-fluid">
        <div class="row mt-5">
            <form class="form-horizontal" method="post" action="" novalidate>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput" id="formGroupExampleInput">Sorozat címe:</label>
                    <input type="text" class="form-control addseriesname  <?php if (isset($errors['addseriesname'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput" placeholder="Sorozat címe" name="addseriesname" value="<?= $_POST['addseriesname'] ?? '' ?>">
                    <div id="formGroupExampleInput" class="invalid-feedback">
                        <?php if (isset($errors['addseriesname'])) : ?>
                            <?= $errors['addseriesname'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput2" id="formGroupExampleInput2" class="formcim">Megjelenés dátuma:</label>
                    <input type="number" min="1900" max="<?= idate("Y") ?>" id="formGroupExampleInput2" class="form-control addseriesyear <?php if (isset($errors['addseriesyear'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput2" placeholder="2022" name="addseriesyear" value="<?= $_POST['addseriesyear'] ?? '' ?>">
                    <div id="formGroupExampleInput2" class="invalid-feedback">
                        <?php if (isset($errors['addseriesyear'])) : ?>
                            <?= $errors['addseriesyear'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput3" id="formGroupExampleInput3" class="formcim">Összefoglaló</label>
                    <input class="form-control addseriesplot <?php if (isset($errors['addseriesplot'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput3" placeholder="Összefoglaló" name="addseriesplot" value="<?= $_POST['addseriesplot'] ?? '' ?>">
                    <div id="formGroupExampleInput3" class="invalid-feedback">
                        <?php if (isset($errors['addseriesplot'])) : ?>
                            <?= $errors['addseriesplot'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput4" id="formGroupExampleInput4" class="formcim">Borítókép-link:</label>
                    <input type="text" class="form-control addcover <?php if (isset($errors['addcover'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput4" placeholder="https://" name="addcover" value="<?= $_POST['addcover'] ?? '' ?>">
                    <div id="formGroupExampleInput4" class="invalid-feedback">
                        <?php if (isset($errors['addcover'])) : ?>
                            <?= $errors['addcover'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput5" id="formGroupExampleInput5" class="formcim">Epizódok száma:</label>
                    <input type="number" min="0" class="form-control addseriesepisodes <?php if (isset($errors['episodes'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput5" placeholder="0" name="episodes" value="<?= $_POST['episodes'] ?? '' ?>">
                    <div id="formGroupExampleInput5" class="invalid-feedback">
                        <?php if (isset($errors['episodes'])) : ?>
                            <?= $errors['episodes'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col text-center">
                            <button class="btn btn-outline-dark btn-sm regbtn" style="border-radius: 0; border-width:2px; margin-top:2.5rem">Hozzáadás</button>
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