<?php
session_start();
include('usersstorage.php');

if (isset($_SESSION['user']) || isset($_SESSION['isadmin']) || isset($_SESSION['loggedin'])) {
    $user = $_SESSION['user'];
    $isadmin = $_SESSION['isadmin'];
    $loggedin = $_SESSION['loggedin'];
}

function validate($post, &$data, &$errors)
{

    $usersstorage = new UsersStorage();


    if (!isset($post['loginname'])) {
        $errors['loginname'] = 'A felhasználónév megadása kötelező!';
    } else if (trim($post['loginname']) === '') {
        $errors['loginname'] = 'A felhasználónév megadása kötelező!';
    } else if (empty(($usersstorage->findOne(['username' => $post['loginname']]))) && $post['loginname'] !== 'admin') {
        $errors['loginname'] = 'Nincs ilyen felhasználónév regisztálva!';
    } else {
        $data['loginname'] = $post['loginname'];
    }

    if (!isset($post['loginpwd'])) {
        $errors['loginpwd'] = 'A jelszó megadása kötelező!';
    } else if (trim($post['loginpwd']) === '') {
        $errors['loginpwd'] = 'A jelszó megadása kötelező!';
    } else if (empty(($usersstorage->findOne(['password' => $post['loginpwd']], ['username' => $post['loginname']]))) && $post['loginpwd'] !== 'admin') {
        $errors['loginpwd'] = 'Rossz jelszó!';
    } else {
        $data['loginpwd'] = $post['loginpwd'];
    }


    return count($errors) === 0;
}


$data = [];
$errors = [];


if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {

        $usersstorage = new UsersStorage();

        $isadmin = false;
        $loginname = $data['loginname'];
        $loginpassword = $data['loginpwd'];
        $user = $usersstorage->findOne(['username' => $loginname]);

        if ($loginname === 'admin' && $loginpassword === 'admin') {
            $isadmin = true;
            $loggedin = true;
        } else {
            $loggedin = true;
        }



        $_SESSION['user'] = $user;
        $_SESSION['isadmin'] = $isadmin;
        $_SESSION['loggedin'] = $loggedin;

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <title>Belépés</title>
    <link rel="icon" type='image/x-icon' href="images/Fasticon-Leopard-Iphone-Movie-Folder.ico">
</head>

<body>
    <div class="container-fluid">
        <?php if (isset($errors['loginname']) || (isset($errors['loginpwd']))) : ?>

            <div class="row text-center">
                <div class="col">
                    <div class="alert alert-danger text-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
                            <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <div class="text-center">
                            <?php if (isset($errors['loginname'])) : ?>
                                <p class="hibauzenet"><?= $errors['loginname'] ?></p>
                            <?php endif ?>
                            <?php if (isset($errors['loginpwd'])) : ?>
                                <p class="hibauzenet"><?= $errors['loginpwd'] ?></p>
                            <?php endif ?>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif ?>
        <div class="row mt-5">
            <form class="form-horizontal" method="post" action="" novalidate>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput" id="formGroupExampleInput">Felhasználónév:</label>
                    <input type="text" class="form-control loginname <?php if (isset($errors['loginname'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput" placeholder="Felhasználónév" name="loginname" value="">
                </div>
                <div class="form-group col-xl-4">
                    <label for="formGroupExampleInput2" id="formGroupExampleInput2" class="formcim">Jelszó:</label>
                    <input type="password" class="form-control loginpwd <?php if (isset($errors['loginpwd'])) : ?>is-invalid <?php endif ?>" id="formGroupExampleInput2" placeholder="Jelszó" name="loginpwd" value="">
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col text-center">
                            <button class="btn btn-outline-dark btn-sm regbtn" style="border-radius: 0; border-width:2px; margin-top:2.5rem">Bejelentkezés</button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>