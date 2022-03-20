<h1 class="nombre-pagina">Panel de Administración</h1>

<?php include_once __DIR__ . "/../templates/barra.php" ?>

<h2>Buscar Citas</h2>

<div class="busqueda">
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo$fecha ?>">
        </div>
    </form>
</div>

<?php
    if(count($citas) === 0) {
        echo "<h2>No Hay Citas en Esta Fecha</h2>";
    }
?>

<div class="citas-admin">
    <ul class="citas">
    <?php 
        $idCitaActual = 0;
        $total = 0;
    ?>
    <?php foreach($citas as $key => $cita): ?>
        <?php if($idCitaActual !== $cita->id): ?>
            <?php if($idCitaActual !== 0): //cierra el li anterior si no es el primero ?>
                </li>
            <?php 
                endif;
                $idCitaActual = $cita->id;
            ?>
            <li>
                <p>ID: <span><?php echo $cita->id ?></span></p>
                <p>Hora: <span><?php echo $cita->hora ?></span></p>
                <p>Cliente: <span><?php echo $cita->cliente ?></span></p>
                <p>Email: <span><?php echo $cita->email ?></span></p>
                <p>Telefono: <span><?php echo $cita->telefono ?></span></p>

                <h3>Servicios:</h3>
        <?php endif; ?>
                <p class="servicio"><?php echo $cita->servicio ." $ ". $cita->precio ?></p>

            <?php
                $total += $cita->precio;
                $idSiguienteCita = $citas[$key+1]->id ?? null;
                if($idSiguienteCita !== $idCitaActual):
            ?>
                <p class="total">Total: <span><?php echo "$ ".$total ?></span></p>

                <form action="/api/eliminar" method="POST">
                    <input type="hidden" name="id" value="<?php echo $cita->id ?>">
                    <input type="submit" class="boton-eliminar" value="Eliminar">
                </form>
            <?php 
                $total = 0;
                endif;
            ?>
    <?php endforeach; ?>    
            </li>
    </ul>
</div>