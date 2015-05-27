<?php require_once('Connections/conexion_libros.php'); ?>
<?php
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE libros SET Nombre=%s, Autor=%s, Cantidad=%s, Precio=%s WHERE IdLibro=%s",
                       GetSQLValueString($_POST['Nombre'], "text"),
                       GetSQLValueString($_POST['Autor'], "text"),
                       GetSQLValueString($_POST['Cantidad'], "int"),
                       GetSQLValueString($_POST['Precio'], "double"),
                       GetSQLValueString($_POST['IdLibro'], "text"));

  mysql_select_db($database_conexion_libros, $conexion_libros);
  $Result1 = mysql_query($updateSQL, $conexion_libros) or die(mysql_error());

  $updateGoTo = "modificar_exitoso.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_conexion_libros, $conexion_libros);
$valor = $_GET['IdLibro'];
$query_modificar_consulta = "SELECT * FROM libros where IdLibro=$valor";
$modificar_consulta = mysql_query($query_modificar_consulta, $conexion_libros) or die(mysql_error());
$row_modificar_consulta = mysql_fetch_assoc($modificar_consulta);
$totalRows_modificar_consulta = mysql_num_rows($modificar_consulta);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
</head>

<body>
<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table align="center">
    <tr valign="baseline">
      <td nowrap align="right">IdLibro:</td>
      <td><?php echo $row_modificar_consulta['IdLibro']; ?></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Nombre:</td>
      <td><input type="text" name="Nombre" value="<?php echo $row_modificar_consulta['Nombre']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Autor:</td>
      <td><input type="text" name="Autor" value="<?php echo $row_modificar_consulta['Autor']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Cantidad:</td>
      <td><input type="text" name="Cantidad" value="<?php echo $row_modificar_consulta['Cantidad']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Precio:</td>
      <td><input type="text" name="Precio" value="<?php echo $row_modificar_consulta['Precio']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="Actualizar registro"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1">
  <input type="hidden" name="IdLibro" value="<?php echo $row_modificar_consulta['IdLibro']; ?>">
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($modificar_consulta);
?>
