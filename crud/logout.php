<?php
session_start();
session_destroy();
header('Location: /~emanuel.torres/index.html');
exit;
