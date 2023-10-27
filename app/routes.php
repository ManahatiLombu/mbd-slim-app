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
    
    $app->post('/karyawan', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parseBody = $request->getParsedBody();
        try {
            if (
                empty($parseBody['id_laundry']) ||
                empty($parseBody['nama']) ||
                empty($parseBody['no_telpon']) ||
                empty($parseBody['alamat'])
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $idLaundry = $parseBody['id_laundry'];
            $nama = $parseBody['nama'];
            $noTelpon = $parseBody['no_telpon'];
            $alamat = $parseBody['alamat'];
    
            $query = $db->prepare('CALL insert_Karyawan(?, ?, ?, ?)');
    
            $query->execute([$idLaundry, $nama, $noTelpon, $alamat]);
    
            $lastId = $db->lastInsertId();
    
            $response->getBody()->write(json_encode(['message' => 'Data Karyawan Tersimpan Dengan ID ' . $lastId]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });

    $app->post('/pelanggan', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parseBody = $request->getParsedBody();
        try {
            if (
                empty($parseBody['nama']) ||
                empty($parseBody['no_telpon']) ||
                empty($parseBody['alamat'])
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $nama = $parseBody['nama'];
            $noTelpon = $parseBody['no_telpon'];
            $alamat = $parseBody['alamat'];
    
            $query = $db->prepare('CALL insert_Pelanggan(?, ?, ?)');
    
            $query->execute([$nama, $noTelpon, $alamat]);
    
            $lastId = $db->lastInsertId();
    
            $response->getBody()->write(json_encode(['message' => 'Data pelanggan Tersimpan Dengan ID ' . $lastId]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });

    // delete

    $app->delete('/transaksi/{id_transaksi}', function (Request $request, Response $response, $args) {
        $currentId = $args['id_transaksi'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL delete_from_transaksi(?)');
            $query->bindParam(1, $currentId, PDO::PARAM_INT);
            $query->execute();
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'transaksi dengan ID ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'transaksi dengan ID ' . $currentId . ' telah dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    $app->put('/karyawan/{id_karyawan}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
         
        $idkaryawan = $args['id_karyawan'];
        $idlaundry = $parsedBody['id_laundry'];
        $nama = $parsedBody['nama'];
        $notelpon = $parsedBody['no_telepon'];
        $alamat = $parsedBody['alamat'];

        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL update_karyawan(?, ?, ?, ?, ?)');
        $query->bindParam(1, $idkaryawan, PDO::PARAM_INT);
        $query->bindParam(2, $idlaundry, PDO::PARAM_INT);
        $query->bindParam(3, $nama, PDO::PARAM_STR);
        $query->bindParam(4, $notelpon, PDO::PARAM_STR);
        $query->bindParam(5, $alamat, PDO::PARAM_STR);
     
  
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'karyawan dengan id ' . $idkaryawan . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate karyawan dengan id ' . $idkaryawan
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    $app->post('/new_pelanggan', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
    
        $idpelanggan = $parsedBody["id_pelanggan"];
        $nama = $parsedBody["nama"];
        $notelpon = $parsedBody["no_telepon"];
        $alamat = $parsedBody["alamat"];
    
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL insert_pelanggan(?, ?, ?, ?)');
            $query->execute([$idpelanggan, $nama, $notelpon, $alamat]);
    
            $responseData = [
                'message' => 'Data new_pelanggan disimpan.'
            ];
    
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $responseData = [
                'error' => 'Terjadi kesalahan dalam penyimpanan data new_pelanggan.'
            ];
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });


        
};
