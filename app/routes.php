<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/transaksi', function(Request $request, Response $response){
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM transaksi');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/transaksi/{id_transaksi}', function(Request $request, Response $response){
        $db = $this->get(PDO::class);

        $id_transaksi = $request->getAttribute('id_transaksi');
        $query = $db->prepare('SELECT * FROM transaksi WHERE id_transaksi=?');
        $query->execute([$id_transaksi]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/karyawan', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $query = $db->query('SELECT * FROM karyawan');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));
    
        return $response->withHeader("Content-Type", "application/json");
    });
    
    $app->post('/pelanggan', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $data = $request->getParsedBody();
    
        $nama = $data['adit'];
        $no_telepon = $data['no_telepon'];
        $alamat = $data['alamat'];
    
        $query = $db->prepare('INSERT INTO pelanggan (nama, no_telepon, alamat) VALUES (maria, 082266752341, jln.kesesatan)');
        $query->execute([$nama, $no_telepon, $alamat]);
    
        $response->getBody()->write(json_encode(['message' => 'Data pelanggan berhasil ditambahkan']));
        return $response->withHeader("Content-Type", "application/json")->withStatus(201);
    });
    
    
};
