<?php include "top.php"; ?>
    <!--
    <div class="alert alert-success">¡Ejemplo mensaje de éxito!</div>
    <div class="alert alert-error">¡Ejemplo mensaje de error!</div>
    -->

    <nav>
        <p><a href="film.php">Volver</a></p>
    </nav>
<?php 
  if(!empty($_REQUEST['film_id']) && !empty($_REQUEST['title'])) {

    $film_id = $_REQUEST['film_id'];
    $title = $_REQUEST['title'];

    // Se actualiza si se enviaron los datos.
    if (isset($_POST['update'])) {
      $link->autocommit(FALSE);

      // Se borran todos y luego añadimos los que vengan con on.
      if (! $link->query("DELETE FROM film_category WHERE film_id = " . $film_id)) {
        $error = "Error al borrar";
      }

      if (empty($error)) {
        $stmt = $link->prepare("INSERT INTO film_category (film_id, category_id, last_update) VALUES (?, ?, CURRENT_TIMESTAMP)");
        $stmt->bind_param("ii", $film_id, $category_id);
   
        $updated = true;
        foreach($_POST as $category_id => $value) {
          if ($value == "on") {
            if (! $stmt->execute()) {
              $updated = false;
            }
          }
        }

        if (! $updated) $error = "Error al actualizar las categorías";

        if(empty($error)) {
          echo "<div class=\"alert alert-success\">Categorías actualizadas</div>";
        } else {
          echo "<div class=\"alert alert-error\">$error</div>";
        }
      }
      $link->commit();
    }

    // Preparo la consulta
    $stmt = $link->prepare("SELECT count(*) as belongs FROM film_category WHERE film_id = ? AND category_id = ?");
    $stmt->bind_param("ii", $film_id, $category_id);
    $stmt->bind_result($belongs);

    $sql = "SELECT * FROM category";
    $result = $link->query($sql);
?>
    <section id="films">
      <h2>Categorías de la pelicula: <?=$title?></h2>
      <form action="category_film.php" method="post">
        <ul>
<?php
    while($row = $result->fetch_assoc()) {
      $category_id = $row['category_id'];
      $stmt->execute();
      $stmt->fetch(); // Un count(*) siempre devuelve una línea aunque sea un 0.
      $checked = $belongs > 0 ? " checked " : "";
?>
          <li>
            <label>
              <input type="checkbox" name="<?=$row['category_id']?>" id="" <?=$checked?>>
              <?=$row['name']?>
            </label>
          </li>
<?php
    }
?>
        </ul>
        <input type="hidden" name="film_id" value="<?=$film_id?>">
        <input type="hidden" name="title" value="<?=$title?>">
        <p>
          <input type="submit" value="Actualizar" name="update">
        </p>
      </form>
    <section>
<?php 
    } else {
?>
        <div class="alert alert-error">No se han enviado los datos de la película</div>
<?php
    }
?>
<?php include "bottom.php"; ?>