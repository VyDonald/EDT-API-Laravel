<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Firestore\FirestoreClient;

try {
    $firestore = new FirestoreClient([
        'projectId' => 'edt-api',
        // 'keyFilePath' => '/home/mashle/credentials.json',
    ]);
    echo "Connexion réussie.\n";

    $firestore->collection('roles')->document('test')->set(['nom' => 'test']);
    echo "Document ajouté.\n";
} catch (\Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
