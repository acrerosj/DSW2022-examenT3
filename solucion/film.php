<?php include "top.php"; ?>
    <!--
    <div class="alert alert-success">¡Ejemplo mensaje de éxito!</div>
    <div class="alert alert-error">¡Ejemplo mensaje de error!</div>
    -->
<?php
  if ($error == null) {
    // Compruebo si está la opción de borrar:
    if(isset($_GET['delete']) && !empty($_GET['category_id'])) {
        $category_id = $_GET['category_id'];
        $sql = "DELETE FROM category WHERE category_id = " . $category_id;
        if ($link->query($sql)) {
            echo "<div class=\"alert alert-success\">Categoría eliminada</div>";
        } else {
          echo "<div class=\"alert alert-error\">No se puede borrar dicha categoría porque tiene películas asociadas a ella.</div>";
        }                    
    }
    $sql = "SELECT * FROM category";
    $result = $link->query($sql);
?>
    <section id="films">
        <h2>Peliculas</h2>
        <form action="film.php" method="get">
          <fieldset>
            <legend>Categorías</legend>
            <select name="category_id" id="">
              <option selected disabled>Elige una categoría</option>
<?php
    while ($row = $result->fetch_assoc()) {
        printf("<option value=\"%s\">%s</option>", $row['category_id'],$row['name']);
    }
    $result->free();
?>
            </select>
            <input type="submit" name="search" value="buscar">
            <input type="submit" name="delete" value="eliminar">
          </fieldset>
        </form>
        <nav>
            <fieldset>
                <legend>Acciones</legend>                    
                <a href="create.php">
                    <button>Crear Categoria</button>
                </a>                    
            </fieldset>
        </nav>
<?php
    if (!empty($_GET['category_id'])) {
        $sql = "SELECT film.film_id, film.title, film.release_year, film.length FROM film, film_category WHERE film.film_id = film_category.film_id AND film_category.category_id = " . $_GET['category_id'];
        $result = $link->query($sql);
        if ($result->num_rows == 0) {
            echo "<h3>No hay películas para esta categoría</h3>";
        } else {
?>
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Año</th>
                    <th>Duración</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
<?php   
            while ($film = $result->fetch_assoc()) {
?>
                <tr>
                    <td><?=$film['title']?></td>
                    <td class="center"><?=$film['release_year']?></td>
                    <td class="center"><?=$film['length']?></td>
                    <td class="actions">                            
                        <a class="button" href="category_film.php?film_id=<?=$film['film_id']?>&title=<?=$film['title']?>">
                            <button>Cambiar categorías</button>
                        </a>               
                    </td>
                </tr>
<?php
            }
?>
            </tbody>
        </table>
<?php 
            }
        }        
?>
    </section>
<?php 
    } else {
        ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
<?php
    }
?>
<?php include "bottom.php"; ?>