<?php include "top.php"; ?>
<section id="create">
    <h2>Nueva categoría</h2>
    <nav>
        <p><a href="film.php">Volver</a></p>
    </nav>
<?php    
    if (!empty($_POST)) {
        if ($error == null) { 
            $name = empty($_POST["name"]) ? "" : $_POST["name"];
            if (empty($name)) $error = "¡Nombre de usuario no válido!";

            if (empty($error)) {
                $sql = "INSERT INTO category (category_id, name, last_update) VALUES (NULL, '" . $_POST['name'] . "', CURRENT_TIMESTAMP)";
                if ($link->query($sql)) {   
                    $created = true;
                } else {
                    $error = "¡El usuario no pudo ser creado!";
                }
            }
        }
        if (!empty($error)) {
    ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php
        } else if (isset($created) && $created) {
        ?>
            <div class="alert alert-success">¡Usuario creado satisfactoriamente!</div>
        <?php
        }
    } else {
   ?>
    <form action="" autocomplete="off" method="post">
        <fieldset>
            <legend>Datos de la categoría</legend>
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" required>
            <p></p>
            <input type="reset" value="Limpiar">            
            <input type="submit" value="Crear">
        </fieldset>
    </form>
    <?php
    }
    ?>
</section>
<?php include "bottom.php"; ?>