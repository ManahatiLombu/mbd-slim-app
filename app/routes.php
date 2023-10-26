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

        $query = $db->query('CALL read_table_transaksi()');
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
    $app->get('/pelanggan/{id_pelanggan}', function(Request $request, Response $response){
        $db = $this->get(PDO::class);

        $id_transaksi = $request->getAttribute('id_pelanggan');
        $query = $db->prepare('SELECT * FROM transaksi WHERE id_pelanggan=?');
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

    $app->get('/paket', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $query = $db->query('SELECT * FROM paket');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));
    
        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/pelanggan', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $query = $db->query('SELECT * FROM pelanggan');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));
    
        return $response->withHeader("Content-Type", "application/json");
    });

    $app->post('/insert_karyawan', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
    
        $id_laundry = $parsedBody['id_laundry'];
        $nama = $parsedBody['nama'];
        $no_telepon = $parsedBody['no_telepon'];
        $alamat = $parsedBody['alamat'];
    
        
        $sql = "INSERT INTO karyawan (id_laundry, nama, no_telepon, alamat) VALUES (:id_laundry, :nama, :no_telepon, :alamat)";
        $stmt = $this->post('db')->prepare($sql);
        $stmt->bindParam(':id_laundry', $id_laundry);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':no_telepon', $no_telepon);
        $stmt->bindParam(':alamat', $alamat);
    
        if ($stmt->execute()) {
            $data = [
                'status' => 'Data karyawan berhasil ditambahkan'
            ];
            return $response->withJson($data, 201);
        } else {
            $data = [
                'status' => 'Gagal menambahkan data karyawan'
            ];
            return $response->withJson($data, 500);
        }
    });

    $app->post('/insert_karyawan', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
    
        $id_karyawan = $parsedBody["id_karyawan"];
        $id_laundry = $parsedBody["id_laundry"];
        $nama = $parsedBody["nama"];
        $no_telepon = $parsedBody["no_telepon"];
        $alamat = $parsedBody["alamat"];
    
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL insert_karyawan(?, ?, ?)');
            $query->execute([$name, $address, $city]);
    
            $responseData = [
                'message' => 'karyawan disimpan.'
            ];
    
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $responseData = [
                'error' => 'Terjadi kesalahan dalam penyimpanan karyawan.'
            ];
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });
};
