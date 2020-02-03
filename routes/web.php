<?php

// GET /
$router->get('/', function () {
    return config('app.name');
});
