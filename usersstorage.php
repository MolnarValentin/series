<?php 
include('storage.php');

class UsersStorage extends Storage {
    public function __construct()
    {
        parent::__construct(new JsonIO('users.json'));
    }
}
?>