<?php
include('usersstorage.php');
//print_r($_POST);

session_start();


if (isset($_SESSION['user']) || isset($_SESSION['isadmin']) || isset($_SESSION['loggedin'])) {
    $user = $_SESSION['user'];
    $isadmin = $_SESSION['isadmin'];
    $loggedin = $_SESSION['loggedin'];
}else{
    $isadmin = false;
    $loggedin = false;
}

//fuggvenyek

function validate($post, &$data, &$errors)
{

    $usersstorage = new UsersStorage();

    if (!isset($post['regfnev'])) {
        $errors['regfnev'] = 'A felhasználónév megadása kötelező!';
    } else if (trim($post['regfnev']) === '') {
        $errors['regfnev'] = 'A felhasználónév megadása kötelező!';
    } else if (empty($usersstorage->findOne(['username' => $post['regfnev']])) === false) {
        $errors['regfnev'] = 'Ilyen felhasználónévvel már regisztráltak!';
    } else {
        $data['regfnev'] = $post['regfnev'];
    }

    if (!isset($post['regemailcim'])) {
        $errors['regemailcim'] = 'Az emailcím megadása kötelező!';
    } else if (trim($post['regemailcim']) === '') {
        $errors['regemailcim'] = 'Az emailcím megadása kötelező!';
    } else if (empty($usersstorage->findOne(['email' => $post['regemailcim']])) === false) {
        $errors['regemailcim'] = 'Ilyen emailcímmel már regisztráltak!';
    } else if (!filter_var($post['regemailcim'], FILTER_VALIDATE_EMAIL)) {
        $errors['regemailcim'] = 'Nem megfelelő email formátum!';
    } else {
        $data['regemailcim'] = $post['regemailcim'];
    }

    if (!isset($post['regjelszo1'])) {
        $errors['regjelszo1'] = 'A jelszó megadása kötelező!';
    } else if (trim($post['regjelszo1']) === '') {
        $errors['regjelszo1'] = 'A jelszó megadása kötelező!';
    } else {
        $data['regjelszo1'] = $post['regjelszo1'];
    }

    if (!isset($post['regjelszo2'])) {
        $errors['regjelszo2'] = 'A jelszó megadása kötelező!';
    } else if (trim($post['regjelszo2']) === '') {
        $errors['regjelszo2'] = 'A jelszó megadása kötelező!';
    } else if ($post['regjelszo1'] !== $post['regjelszo2']) {
        $errors['regjelszo2'] = 'A megadott jelszók nem egyeznek!';
    } else {
        $data['regjelszo2'] = $post['regjelszo2'];
    }



    return count($errors) === 0;
}


//foprogram

$data = [];
$errors = [];


if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        //beolvasas
        $regfnev = $data['regfnev'];
        $regemailcim = $data['regemailcim'];
        $regjelszo1 = $data['regjelszo1'];
        $regjelszo2 = $data['regjelszo2'];
        //feldolgozas
        $usersstorage = new UsersStorage();

        $usersstorage->add([
            'username' => $regfnev,
            'email' => $regemailcim,
            'password' => $regjelszo1
        ]);

        header('location: login.php');
        exit();
    }
}


//kiiras
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <title>Regisztráció</title>
    <link rel="icon" type='image/x-icon' href="images/Fasticon-Leopard-Iphone-Movie-Folder.ico">
</head>

<body>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="row col-md-auto">
                <table class="registererrorslist text-center">
                    <td class="hibauzenet text-center"></td>
                </table>
            </div>
            <form class="form-horizontal" method="post" action="" novalidate>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput" id="formGroupExampleInput">Felhasználónév:</label>
                    <input type="text" class="form-control regfnev <?php if (isset($errors['regfnev'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput" placeholder="Felhasználónév" name="regfnev" value="<?= $_POST['regfnev'] ?? '' ?>">
                    <div id="formGroupExampleInput" class="invalid-feedback">
                        <?php if (isset($errors['regfnev'])) : ?>
                            <?= $errors['regfnev'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput2" id="formGroupExampleInput2" class="formcim">Email-cím:</label>
                    <input type="email" class="form-control regemailcim <?php if (isset($errors['regemailcim'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput2" placeholder="Email-cím" name="regemailcim" value="<?= $_POST['regemailcim'] ?? '' ?>">
                    <div id="formGroupExampleInput2" class="invalid-feedback">
                        <?php if (isset($errors['regemailcim'])) : ?>
                            <?= $errors['regemailcim'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput3" id="formGroupExampleInput3" class="formcim">Jelszó először:</label>
                    <input type="password" class="form-control regjelszo1 <?php if (isset($errors['regjelszo1'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput3" placeholder="Jelszó először" name="regjelszo1" value="<?= $_POST['regjelszo1'] ?? '' ?>">
                    <div id="formGroupExampleInput3" class="invalid-feedback">
                        <?php if (isset($errors['regjelszo1'])) : ?>
                            <?= $errors['regjelszo1'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput4" id="formGroupExampleInput4" class="formcim">Jelszó másodszor:</label>
                    <input type="password" class="form-control regjelszo2 <?php if (isset($errors['regjelszo2'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput4" placeholder="Jelszó másodszor" name="regjelszo2" value="<?= $_POST['regjelszo2'] ?? '' ?>">
                    <div id="formGroupExampleInput4" class="invalid-feedback">
                        <?php if (isset($errors['regjelszo2'])) : ?>
                            <?= $errors['regjelszo2'] ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col text-center">
                            <button class="btn btn-outline-dark btn-sm regbtn" style="border-radius: 0; border-width:2px; margin-top:2.5rem">Regisztráció</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <a class="btn btn-outline-dark btn-sm regbtn" style="border-radius: 0; border-width:2px; margin-top:0.5rem" href="index.php">Vissza/mégse</a>
                </div>
            </div>
        </div>
    </div>
    </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>