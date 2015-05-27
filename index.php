<?php require_once('Connections/conexion_libros.php'); ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO libros (IdLibro, Nombre, Autor, Cantidad, Precio) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IdLibro'], "text"),
                       GetSQLValueString($_POST['Nombre'], "text"),
                       GetSQLValueString($_POST['Autor'], "text"),
                       GetSQLValueString($_POST['Cantidad'], "int"),
                       GetSQLValueString($_POST['Precio'], "double"));

  mysql_select_db($database_conexion_libros, $conexion_libros);
  $Result1 = mysql_query($insertSQL, $conexion_libros) or die(mysql_error());

  $insertGoTo = "ingreso_exitoso.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$maxRows_consulta_libros = 10;
$pageNum_consulta_libros = 0;
if (isset($_GET['pageNum_consulta_libros'])) {
  $pageNum_consulta_libros = $_GET['pageNum_consulta_libros'];
}
$startRow_consulta_libros = $pageNum_consulta_libros * $maxRows_consulta_libros;

mysql_select_db($database_conexion_libros, $conexion_libros);
$query_consulta_libros = "SELECT * FROM libros";
$query_limit_consulta_libros = sprintf("%s LIMIT %d, %d", $query_consulta_libros, $startRow_consulta_libros, $maxRows_consulta_libros);
$consulta_libros = mysql_query($query_limit_consulta_libros, $conexion_libros) or die(mysql_error());
$row_consulta_libros = mysql_fetch_assoc($consulta_libros);

if (isset($_GET['totalRows_consulta_libros'])) {
  $totalRows_consulta_libros = $_GET['totalRows_consulta_libros'];
} else {
  $all_consulta_libros = mysql_query($query_consulta_libros);
  $totalRows_consulta_libros = mysql_num_rows($all_consulta_libros);
}
$totalPages_consulta_libros = ceil($totalRows_consulta_libros/$maxRows_consulta_libros)-1;

$queryString_consulta_libros = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_consulta_libros") == false && 
        stristr($param, "totalRows_consulta_libros") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_consulta_libros = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_consulta_libros = sprintf("&totalRows_consulta_libros=%d%s", $totalRows_consulta_libros, $queryString_consulta_libros);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Prueba Base Libros</title>
</head>

<body>
<table border="1">
  <tr>
    <td><div align="center"><strong>C&oacute;digo</strong></div></td>
    <td><div align="center"><strong>Nombre</strong></div></td>
    <td><div align="center"><strong>Autor</strong></div></td>
    <td><div align="center"><strong>Cantidad</strong></div></td>
    <td><div align="center"><strong>Precio</strong></div></td>
    <td colspan="2"><div align="center"><strong>Operaciones</strong></div></td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_consulta_libros['IdLibro']; ?></td>
      <td><?php echo $row_consulta_libros['Nombre']; ?></td>
      <td><?php echo $row_consulta_libros['Autor']; ?></td>
      <td><div align="center"><?php echo $row_consulta_libros['Cantidad']; ?></div></td>
      <td><div align="center"><?php echo $row_consulta_libros['Precio']; ?></div></td>
      <td><a href="modificar.php?IdLibro=<?php echo $row_consulta_libros['IdLibro']; ?>">Modificar</a></td>
      <td><a href="borrar.php?IdLibro=<?php echo $row_consulta_libros['IdLibro']; ?>">Eliminar</a></td>
    </tr>
    <?php } while ($row_consulta_libros = mysql_fetch_assoc($consulta_libros)); ?>
</table>
<p>
<table border="0" width="50%" align="center">
  <tr>
    <td width="23%" align="center"><?php if ($pageNum_consulta_libros > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_consulta_libros=%d%s", $currentPage, 0, $queryString_consulta_libros); ?>">Primero</a>
          <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center"><?php if ($pageNum_consulta_libros > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_consulta_libros=%d%s", $currentPage, max(0, $pageNum_consulta_libros - 1), $queryString_consulta_libros); ?>">Anterior</a>
          <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center"><?php if ($pageNum_consulta_libros < $totalPages_consulta_libros) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_consulta_libros=%d%s", $currentPage, min($totalPages_consulta_libros, $pageNum_consulta_libros + 1), $queryString_consulta_libros); ?>">Siguiente</a>
          <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center"><?php if ($pageNum_consulta_libros < $totalPages_consulta_libros) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_consulta_libros=%d%s", $currentPage, $totalPages_consulta_libros, $queryString_consulta_libros); ?>">&Uacute;ltimo</a>
          <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
</p>
<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table align="center">
    <tr valign="baseline">
      <td nowrap align="right">IdLibro:</td>
      <td><input type="text" name="IdLibro" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Nombre:</td>
      <td><input type="text" name="Nombre" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Autor:</td>
      <td><input type="text" name="Autor" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Cantidad:</td>
      <td><input type="text" name="Cantidad" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Precio:</td>
      <td><input type="text" name="Precio" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="Insertar registro"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1">
</form>
<p><a href="libros.php">Ver libros </a></p>
</body>
</html>
<?php
mysql_free_result($consulta_libros);
?>
