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

      // Obtener id de las categorias a las que ya pertenecía la película.
      $sql = "SELECT category_id FROM film_category WHERE film_id = " . $film_id;
      $result = $link->query($sql);
      $old_ids = array_map(fn($row) => $row[0], $result->fetch_all());
      printf("<p>old_ids: %s</p>", implode('-',$old_ids));

      // listado de ids de las categorías a las que ahora va a pertenecer las películas.
      $new_ids = isset($_POST['category_ids']) ? $_POST['category_ids'] : [];
      printf("<p>new_ids: %s</p>", implode('-',$new_ids));

      // Listado de las ids que antes estaban y ahora no, Por tanto, hay que borrarlas.
      $delete_ids = array_diff($old_ids, $new_ids);
      printf("<p>delete_ids: %s</p>", implode('-',$delete_ids));
      // Listado de las ids que ahora están y antes no. Por tanto, hay que añadirlas.
      $create_ids = array_diff($new_ids, $old_ids);
      printf("<p>create_ids: %s</p>", implode('-',$create_ids));

      // Se borran solo los ids que ya no están: delete_ids.
      if (count($delete_ids)>0) {
        $sql = "DELETE FROM film_category WHERE film_id = " . $film_id . " AND category_id IN (" . implode(',',$delete_ids) . ")";
        printf("<p>%s</p>", $sql);
        if (! $link->query($sql)) {
          $error = "Error al borrar";
        }
      }

      if (empty($error)) {
        $stmt = $link->prepare("INSERT INTO film_category (film_id, category_id, last_update) VALUES (?, ?, CURRENT_TIMESTAMP)");
        $stmt->bind_param("ii", $film_id, $category_id);
   
        $updated = true;
        foreach($create_ids as $category_id) {
          printf("<p>Creando el id: %s", $category_id);
          if ($updated && !$stmt->execute()) {  // Si updated ya es falso, ni siquiera hace la inserción.
            $updated = false;
          }
        }

        if (! $updated) $error = "Error al actualizar las categorías";

        if(empty($error)) {
          echo "<div class=\"alert alert-success\">Categorías actualizadas</div>";
          $link->commit();
        } else {
          echo "<div class=\"alert alert-error\">$error</div>";
          $link->rollback();
        }
      }
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
              <input type="checkbox" name="category_ids[]" value="<?=$row['category_id']?>" id="" <?=$checked?>>
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