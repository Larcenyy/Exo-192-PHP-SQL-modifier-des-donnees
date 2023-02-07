<?php

class DbPDO
{
    private static string $server = 'localhost';
    private static string $username = 'root';
    private static string $password = '';
    private static string $database = 'table_test_php';
    private static ?PDO $db = null;

    public static function connect(): ?PDO {
        if (self::$db == null){
            try {
                self::$db = new PDO("mysql:host=".self::$server.";dbname=".self::$database, self::$username, self::$password);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$db->beginTransaction();

                $nom = "Ceci est mon test";
                $id = self::$db->lastInsertId();

                $request = self::$db->prepare("UPDATE user SET prenom = :prenom WHERE id = :id");
                $request->bindParam(":id", $id);
                $request->bindParam(":prenom", $nom);

                $result = $request->execute();
                if($result) {
                    $id = self::$db->lastInsertId();
                    echo "Utilisateur modifié";
                    self::$db->commit(); // Envoie les requêtes au serveur
                } else {
                    echo "Echec de la modification de l'utilisateur";
                    self::$db->rollBack(); // Annule les modifications en cas d'erreur
                }
            }
            catch (PDOException $e) {
                echo "Erreur de la connexion à la dn : " . $e->getMessage();
                self::$db->rollBack(); // On restaure les anciens données en cas d'erreur
            }
        }
        return self::$db;
    }
}