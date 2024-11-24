<?php
session_start();
session_destroy();
// Redirect with a success message in the URL
header('Location: ../login.php?logout=success');
exit();
?>
