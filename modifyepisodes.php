<?php
session_start();
include('seriesstorage.php');


$isadmin = false;
$loggedin = false;

if (isset($_SESSION['user']) || isset($_SESSION['isadmin']) || isset($_SESSION['loggedin'])) {
    $user = $_SESSION['user'];
    $isadmin = $_SESSION['isadmin'];
    $loggedin = $_SESSION['loggedin'];
    if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] === false) {
        header('Location: reszletezooldal.php');
        exit();
    }
}



if (!isset($_GET['sid'])) {
    header('Location: addseries.php');
    exit();
}

if (!isset($_GET['eid'])) {
    header('Location: addseries.php');
    exit();
}

function isRealDate($date)
{
    if (false === strtotime($date)) {
        return false;
    }
    list($year, $month, $day) = explode('-', $date);
    return checkdate($month, $day, $year);
}



$id = $_GET['sid'];
$eid = $_GET['eid'];

$seriesstorage = new SeriesStorage();

$series = $seriesstorage->findById($id);

if (!$series) {
    $errors['global'] = 'Nem létező id!';
}

$data = [];
$errors = [];

function validate($post, &$data, &$errors)
{



    if (!isset($post['addepisodename'])) {
        $errors['addepisodename'] = 'Az epizód nevének megadása kötelező!';
    } else if (trim($post['addepisodename']) === '') {
        $errors['addepisodename'] = 'Az epizód nevének megadása kötelező!';
    } else {
        $data['addepisodename'] = $post['addepisodename'];
    }


    if (!isset($post['addepisodeyear'])) {
        $errors['addepisodeyear'] = 'Az év megadása kötelező!';
    } else if (trim($post['addepisodeyear']) === '') {
        $errors['addepisodeyear'] = 'Az év megadása kötelező!';
    } else if (isRealDate($post['addepisodeyear']) === false) {
        $errors['addepisodeyear'] = 'Nem megfelelő dátum formátum!';
    } else {
        $data['addepisodeyear'] = $post['addepisodeyear'];
        if ($data['addepisodeyear'] < 1900 - 01 - 01 || $data['addepisodeyear'] > date("Y-m-d")) {
            $errors['addepisodeyear'] = 'Dátum nem lehet kissebb 1900-nál, vagy nagyobb a mai dátumnál!';
        }
    }

    if (!isset($post['addepisodeplot'])) {
        $errors['addepisodeplot'] = 'Összefoglaló megadása kötelező!';
    } else if (trim($post['addepisodeplot']) === '') {
        $errors['addepisodeplot'] = 'Összefoglaló megadása kötelező!';
    } else {
        $data['addepisodeplot'] = $post['addepisodeplot'];
    }


    if (!isset($post['addepisoderating'])) {
        $errors['addepisoderating'] = 'Az értékelés megadása kötelező!';
    } else if (trim($post['addepisoderating']) === '') {
        $errors['addepisoderating'] = 'Az értékelés megadása kötelező!';
    } else if (!filter_var($post['addepisoderating'], FILTER_VALIDATE_FLOAT)) {
        $errors['addepisoderating'] = 'Nem megfelelő szám formátum!';
    } else {
        $data['addepisoderating'] = (float)$post['addepisoderating'];
    }



    return count($errors) === 0;
}


//foprogram




if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {

        $addepisodename = $data['addepisodename'];
        $addepisodeyear = $data['addepisodeyear'];
        $addepisodeplot = $data['addepisodeplot'];
        $addepisoderating = $data['addepisoderating'];

        unset($data['addepisodename']);
        unset($data['addepisodeyear']);
        unset($data['addepisodeplot']);
        unset($data['addepisoderating']);


        $data['id'] = $series['id'];
        $data['year'] = $series['year'];
        $data['title'] = $series['title'];
        $data['plot'] = $series['plot'];
        $data['cover'] = $series['cover'];

        $series['episodes'][$eid]['id'] = $eid;
        $series['episodes'][$eid]['date'] = $addepisodeyear;
        $series['episodes'][$eid]['title'] = $addepisodename;
        $series['episodes'][$eid]['plot'] = $addepisodeplot;
        $series['episodes'][$eid]['rating'] = $addepisoderating;

        $data['episodes'] = array();

        $i = 1;

        foreach ($series['episodes'] as $ep) {
            $data['episodes'][$i]['id'] = $series['episodes'][$i]['id'];
            $data['episodes'][$i]['date'] = $series['episodes'][$i]['date'];
            $data['episodes'][$i]['title'] = $series['episodes'][$i]['title'];
            $data['episodes'][$i]['plot'] = $series['episodes'][$i]['plot'];
            $data['episodes'][$i]['rating'] = $series['episodes'][$i]['rating'];
            $i++;
        }



        $seriesstorage->update($id, $data);


        header("location: reszletezooldal.php?id=$id");
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
    <title>Epizód Módosítása</title>
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
                    <label id="formGroupExampleInput" for="formGroupExampleInput">Epizód címe:</label>
                    <input type="text" class="form-control addepisodename <?php if (isset($errors['addepisodename'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput" placeholder="Epizód címe" name="addepisodename" value="<?= $_POST['addepisodename'] ?? $series['episodes'][$eid]['title'] ?? '' ?>">
                    <div id="formGroupExampleInput" class="invalid-feedback">
                        <?php if (isset($errors['addepisodename'])) : ?>
                            <?= $errors['addepisodename'] ?>
                        <?php endif ?>
                    </div>

                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput2" id="formGroupExampleInput2" class="formcim">Megjelenés dátuma:</label>
                    <input type="date" min="1900-01-01" max="<?= date("Y-m-d") ?>" class="form-control addepisodeyear <?php if (isset($errors['addepisodeyear'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput2" placeholder="2022" name="addepisodeyear" value="<?= $_POST['addepisodeyear'] ?? $series['episodes'][$eid]['date'] ?? '' ?>">
                    <div id="formGroupExampleInput2" class="invalid-feedback">
                        <?php if (isset($errors['addepisodeyear'])) : ?>
                            <?= $errors['addepisodeyear'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput3" id="formGroupExampleInput3" class="formcim">Összefoglaló</label>
                    <input class="form-control addepisodeplot <?php if (isset($errors['addepisodeplot'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput3" placeholder="Összefoglaló" name="addepisodeplot" value="<?= $_POST['addepisodeplot'] ?? $series['episodes'][$eid]['plot'] ?? '' ?>">
                    <div id="formGroupExampleInput3" class="invalid-feedback">
                        <?php if (isset($errors['addepisodeplot'])) : ?>
                            <?= $errors['addepisodeplot'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput4" class="formcim <?php if (isset($errors['addepisoderating'])) : ?>is-invalid <?php endif ?>">Értékelés:</label>
                    <input type="number" step="0.1" min="0" max="10" class="form-control addepisoderating <?php if (isset($errors['addepisoderating'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput4" placeholder="0" name="addepisoderating" value="<?= $_POST['addepisoderating'] ?? $series['episodes'][$eid]['rating'] ?? '' ?>">
                    <div id="formGroupExampleInput4" class="invalid-feedback">
                        <?php if (isset($errors['addepisoderating'])) : ?>
                            <?= $errors['addepisoderating'] ?>
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
                        <a class="btn btn-outline-dark btn-sm regbtn" style="border-radius: 0; border-width:2px; margin-top:0.5rem" href="reszletezooldal.php?id=<?= $series['id'] ?>">Vissza/mégse</a>
                    </div>
                </div>

            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>