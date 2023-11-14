<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/laundry', function(Request $request, Response $response){
        $db = $this->get(PDO::class);

        $query = $db->query('CALL read_table_laundry()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/transaksi', function(Request $request, Response $response){
        $db = $this->get(PDO::class);

        $query = $db->query('CALL read_table_transaksi()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    
    $app->get('/karyawan', function(Request $request, Response $response){
        $db = $this->get(PDO::class);

        $query = $db->query('CALL read_table_karyawan()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/paket', function(Request $request, Response $response){
        $db = $this->get(PDO::class);

        $query = $db->query('CALL read_table_paket()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/pelanggan', function(Request $request, Response $response){
        $db = $this->get(PDO::class);

        $query = $db->query('CALL read_table_pelanggan()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

// post
    $app->post('/laundry', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parseBody = $request->getParsedBody();
        try {
            if (
                empty($parseBody['nama']) ||
                empty($parseBody['no_telepon']) ||
                empty($parseBody['alamat']) ||
                empty($parseBody['jenis_laundry'])
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $nama = $parseBody['nama'];
            $noTelpon = $parseBody['no_telepon'];
            $alamat = $parseBody['alamat'];
            $jenisLaundry = $parseBody['jenis_laundry'];
            
    
            $query = $db->prepare('CALL Insert_Laundry(?, ?, ?, ?)');
    
            $query->execute([$nama, $noTelpon, $alamat, $jenisLaundry]);
    
            $lastId = $db->lastInsertId();
    
            $response->getBody()->write(json_encode(['message' => 'Data laundry Tersimpan Dengan ID ' . $lastId]));
    
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
    
    $app->post('/transaksi', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parseBody = $request->getParsedBody();
        try {
            if (
                empty($parseBody['id_karyawan']) ||
                empty($parseBody['id_pelanggan']) ||
                empty($parseBody['id_paket']) ||
                empty($parseBody['tanggal_masuk']) ||
                empty($parseBody['tanggal_keluar']) ||
                empty($parseBody['jenis_paket']) ||
                empty($parseBody['status']) ||
                empty($parseBody['total'])

            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $idkaryawan = $parseBody['id_karyawan'];
            $idPelanggan = $parseBody['id_pelanggan'];
            $idPaket = $parseBody['id_paket'];
            $tanggalMasuk = $parseBody['tanggal_masuk'];
            $tanggalKeluar = $parseBody['tanggal_keluar'];
            $jenisPaket = $parseBody['jenis_paket'];
            $status = $parseBody['status'];
            $total = $parseBody['total'];
            
    
            $query = $db->prepare('CALL insert_Transaksi(?, ?, ?, ?, ?, ?, ?, ?)');
    
            $query->execute([$idkaryawan, $idPelanggan, $idPaket, $tanggalMasuk, $tanggalKeluar, $jenisPaket, $status, $total]);
    
            $lastId = $db->lastInsertId();
    
            $response->getBody()->write(json_encode(['message' => 'Data Transaksi Tersimpan Dengan ID ' . $lastId]));
    
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

    $app->post('/paket', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parseBody = $request->getParsedBody();
        try {
            if (
                empty($parseBody['jenis_paket']) ||
                empty($parseBody['biaya'])
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $jenisPaket = $parseBody['jenis_paket'];
            $biaya = $parseBody['biaya'];
            
    
            $query = $db->prepare('CALL insert_Paket(?, ?)');
    
            $query->execute([$jenisPaket, $biaya]);
    
            $lastId = $db->lastInsertId();
    
            $response->getBody()->write(json_encode(['message' => 'Data Paket Tersimpan Dengan ID ' . $lastId]));
    
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

    $app->post('/karyawan', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parseBody = $request->getParsedBody();
        try {
            if (
                empty($parseBody['id_laundry']) ||
                empty($parseBody['nama']) ||
                empty($parseBody['no_telepon']) ||
                empty($parseBody['alamat'])
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $idLaundry = $parseBody['id_laundry'];
            $nama = $parseBody['nama'];
            $noTelpon = $parseBody['no_telepon'];
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
                empty($parseBody['no_telepon']) ||
                empty($parseBody['alamat'])
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $nama = $parseBody['nama'];
            $noTelpon = $parseBody['no_telepon'];
            $alamat = $parseBody['alamat'];
            
    
            $query = $db->prepare('CALL insert_Pelanggan(?, ?, ?)');
    
            $query->execute([$nama, $noTelpon, $alamat]);
    
            $lastId = $db->lastInsertId();
    
            $response->getBody()->write(json_encode(['message' => 'Data Pelanggan Tersimpan Dengan ID ' . $lastId]));
    
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
    $app->delete('/laundry/{id_laundry}', function (Request $request, Response $response, $args) {
        $currentId = $args['id_laundry'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL delete_from_laundry(?)');
            $query->bindParam(1, $currentId, PDO::PARAM_INT);
            $query->execute();
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'laundry dengan ID ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'laundry dengan ID ' . $currentId . ' telah dihapus dari database'
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

    $app->delete('/pelanggan/{id_pelanggan}', function (Request $request, Response $response, $args) {
        $currentId = $args['id_pelanggan'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL delete_from_pelanggan(?)');
            $query->bindParam(1, $currentId, PDO::PARAM_INT);
            $query->execute();
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'pelanggan dengan ID ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'pelanggan dengan ID ' . $currentId . ' telah dihapus dari database'
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

    $app->delete('/karyawan/{id_karyawan}', function (Request $request, Response $response, $args) {
        $currentId = $args['id_karyawan'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL delete_from_karyawan(?)');
            $query->bindParam(1, $currentId, PDO::PARAM_INT);
            $query->execute();
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'karyawan dengan ID ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'karyawan dengan ID ' . $currentId . ' telah dihapus dari database'
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

    
    $app->delete('/paket/{id_paket}', function (Request $request, Response $response, $args) {
        $currentId = $args['id_paket'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL delete_from_paket(?)');
            $query->bindParam(1, $currentId, PDO::PARAM_INT);
            $query->execute();
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'paket dengan ID ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'paket dengan ID ' . $currentId . ' telah dihapus dari database'
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



// put
    $app->put('/laundry/{id_laundry}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
         
        $nama = $parsedBody['nama'];
        $noTelpon = $parsedBody['no_telepon'];
        $alamat = $parsedBody['alamat'];
        $jenisLaundry = $parsedBody['jenis_laundry'];

        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL update_paket(?, ?, ?, ?)');
        $query->bindParam(1, $nama, PDO::PARAM_STR);
        $query->bindParam(2, $noTelpon, PDO::PARAM_STR);
        $query->bindParam(3, $alamat, PDO::PARAM_STR);
        $query->bindParam(4, $jenisLaundry, PDO::PARAM_STR);
     
  
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'laundry dengan id ' . $idlaundry . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate laundry dengan id ' . $idpaket
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });


    $app->put('/transaksi/{id_transak}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        
        $idkaryawan = $parsedBody["id_karyawan"];
        $idpelanggan = $parsedBody["id_pelanggan"];
        $idpaket = $parsedBody["id_paket"];
        $tanggalmasuk = $parsedBody["tanggal_masuk"];
        $tanggal_keluar = $parsedBody["tanggal_keluar"];
        $jenis_paket = $parsedBody["jenis_paket"];
        $status = $parsedBody["status"];
        $total = $parsedBody["total"];

        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL update_transaksi(?, ?, ?, ?, ?, ?, ?, ?)');
        $query->bindParam(1, $idkaryawan, PDO::PARAM_INT);
        $query->bindParam(2, $idpelanggan, PDO::PARAM_INT);
        $query->bindParam(3, $idpaket, PDO::PARAM_STR);
        $query->bindParam(4, $tanggalmasuk, PDO::PARAM_STR);
        $query->bindParam(5, $tanggalkeluar, PDO::PARAM_STR);
        $query->bindParam(6, $jenisPaket, PDO::PARAM_STR);
        $query->bindParam(7, $status, PDO::PARAM_STR);
        $query->bindParam(8, $total, PDO::PARAM_STR);
    

        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'transaksi dengan id ' . $idtransaksi . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate transaksi dengan id ' . $idtransaksi
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

    $app->put('/paket/{id_paket}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
         
        $jenisPaket = $parsedBody['jenis_paket'];
        $biaya = $parsedBody['biaya'];

        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL update_paket(?, ?)');
        $query->bindParam(1, $njenisPaket, PDO::PARAM_STR);
        $query->bindParam(2, $biaya, PDO::PARAM_STR);
     
  
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'paket dengan id ' . $idpaket . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate paket dengan id ' . $idpaket
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    $app->put('/pelanggan/{id_pelanggan}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
         
        $nama = $parsedBody['nama'];
        $notelpon = $parsedBody['no_telepon'];
        $alamat = $parsedBody['alamat'];

        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL update_pelanggan(?, ?, ?)');
        $query->bindParam(1, $nama, PDO::PARAM_STR);
        $query->bindParam(2, $notelpon, PDO::PARAM_STR);
        $query->bindParam(3, $alamat, PDO::PARAM_STR);
     
  
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'pelanggan dengan id ' . $idpelanggan . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate pelanggan dengan id ' . $idpelanggan
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });
    main : 


        
};
