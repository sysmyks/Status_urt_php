<?php
// Configuration du serveur Urban Terror
return [
    'server_address' => '127.0.0.1', // Adresse IP du serveur
    'server_port' => 27960, // Port du serveur
    'timeout' => 5, // Délai d'attente pour la connexion
    'cache_duration' => 60, // durée du cache en secondes
    'maps_directory' => '/home/urt/UrbanTerror43/q3ut4', // Dossier contenant les maps
    'local_images_directory' => __DIR__ . '/../images/maps', // Dossier local pour stocker les images
    'mapinfo_file' => '/home/urt/spunkybot-1.13.0/mod/mapinfo.json',
    'records_file' => '/home/urt/spunkybot-1.13.0/mod/jump_records.json'
];