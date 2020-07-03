<!DOCTYPE html>
<html lang="en">
    <?php view('template/header', get_defined_vars()) ?>
    <body>
        <?php view('template/navbar', get_defined_vars());  ?>

        <section id="main_body">
            <?php
            if (isset($viewFile)) {
                view($viewFile, get_defined_vars());
            }
            ?>
        </section>

        <?php view('template/footer')  ?>
    </body>
</html>

