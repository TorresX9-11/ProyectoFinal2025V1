<?php
$id=intval($_GET['id']);
$ch=curl_init("../api/proyectos.php/$id");
curl_setopt_array($ch,[
  CURLOPT_CUSTOMREQUEST=>'DELETE',
  CURLOPT_RETURNTRANSFER=>true
]);
curl_exec($ch); curl_close($ch);
header("Location: index.php"); exit;
?>
