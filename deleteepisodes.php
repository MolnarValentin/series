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

if (!isset($_GET['eid'])) {
    header('Location: reszletezooldal.php');
    exit();
}

if (!isset($_GET['sid'])) {
    header('Location: reszletezooldal.php');
    exit();
}

$id = $_GET['sid'];
$eid = $_GET['eid'];

$seriesStorage = new SeriesStorage();

$series = $seriesStorage->findById($id);

if (!$series) {
    $errors['global'] = 'Nem létező id!';
}


$data['id'] = $series['id'];
$data['year'] = $series['year'];
$data['title'] = $series['title'];
$data['plot'] = $series['plot'];
$data['cover'] = $series['cover'];

$series['episodes'][$eid] = null;

$data['episodes'] = array();

$i = 1;
$j = 1;

foreach ($series['episodes'] as $ep) {
    if (isset($ep)) {
        $data['episodes'][$i]['id'] = $series['episodes'][$j]['id'];
        $data['episodes'][$i]['date'] = $series['episodes'][$j]['date'];
        $data['episodes'][$i]['title'] = $series['episodes'][$j]['title'];
        $data['episodes'][$i]['plot'] = $series['episodes'][$j]['plot'];
        $data['episodes'][$i]['rating'] = $series['episodes'][$j]['rating'];
        $i++;
        $j++;
    } else {
        $j++;
    }
}



$seriesStorage->update($id, $data);


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