<?php

    var_dump(
        pg_connect("
            host=localhost
            port=5432
            dbname=postgres
            user=postgres
            password=Studier2022!"
        )
    );
?>