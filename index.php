<?php
$config = require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/lib/Server.php';
require_once __DIR__ . '/lib/StatusParser.php';
require_once __DIR__ . '/lib/MapImageManager.php';
require_once __DIR__ . '/lib/MapDataManager.php';

$server = new Server($config['server_address'], $config['server_port']);
$formattedStatus = $server->getStatus(); // Les données sont déjà parsées

$mapImageManager = new MapImageManager($config['maps_directory'], $config['local_images_directory']);
$mapImageUrl = $mapImageManager->getMapImage($formattedStatus['mapname']);
$mapDataManager = new MapDataManager($config['mapinfo_file'], $config['records_file']);
$mapInfo = $mapDataManager->getMapInfo($formattedStatus['mapname']);
$mapRecord = $mapDataManager->getMapRecord($formattedStatus['mapname']);

if (!extension_loaded('zip')) {
    die('L\'extension ZIP est requise pour cette application.');
}

if (!file_exists(__DIR__ . '/' . $mapImageUrl)) {
    $mapImageUrl = 'images/maps/default.jpg';
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statut du Serveur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        
        .navbar {
            background-color:rgb(36, 36, 36);
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 100%;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .logo {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-button {
            background-color: rgba(0, 0, 0, 0.1);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .nav-button:hover {
            background-color: rgba(10, 10, 10, 0.85);
        }

        .content {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .active {
            background-color: rgba(255,255,255,0.3);
        }
        .server-status {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .map-image {
            width: 100%;
            height: 200px;
            background-size: cover;
            background-position: center;
            background-color: #ddd;
        }
        .server-info {
            padding: 20px;
        }
        .server-name {
            font-size: 24px;
            color: #333;
            margin: 0 0 15px 0;
        }
        .map-image {
            width: 100%;
            height: 300px; /* Augmenté de 200px à 300px */
            background-size: contain; /* Changé de 'cover' à 'contain' */
            background-position: center;
            background-repeat: no-repeat; /* Ajouté pour éviter la répétition */
            background-color: #2a2a2a; /* Fond plus sombre pour mieux voir l'image */
            transition: height 0.3s ease;
        }
        .map-image:hover {
            height: 400px; /* L'image s'agrandit au survol */
        }
        .info-item {
            padding: 10px;
            background-color: #f8f8f8;
            border-radius: 4px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 14px;
        }
        .info-value {
            color: #333;
            font-size: 16px;
            margin-top: 5px;
        }
        .record {
            background-color: #fff3dc !important;
            border: 1px solid #ffd700;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        .players-list {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .player-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            background-color: #f8f8f8;
            margin-bottom: 5px;
            border-radius: 4px;
        }
        .player-name {
            flex-grow: 1;
            font-weight: bold;
        }
        .player-ping {
            color: #666;
            margin-left: 10px;
        }

        /* Media queries pour les appareils mobiles */
        @media screen and (max-width: 600px) {
            .nav-container {
                flex-direction: column;
                text-align: center;
            }

            .nav-links {
                width: 100%;
                justify-content: center;
                margin-top: 10px;
            }

            .nav-button {
                padding: 8px 15px;
                font-size: 0.9rem;
            }

            .logo {
                margin-bottom: 10px;
            }
        }

        .all-records {
            margin-top: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .records-table th,
        .records-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .records-table th {
            background-color: #f8f8f8;
            font-weight: bold;
            color: #666;
        }

        .records-table tr:hover {
            background-color: #f8f8f8;
        }

        .all-records h2 {
            color: #333;
            margin: 0 0 15px 0;
            font-size: 20px;
        }

        .best-time {
            background-color: #fff3dc;
        }

        .best-time td {
            font-weight: bold;
        }

        .all-records h3 {
            margin: 20px 0 10px 0;
            color: #666;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/index.php" class="logo">LaFumisterie</a>
            <div class="nav-links">
                <a href="/index.php" class="nav-button">Dépôt</a>
                <a href="/status-v1/index.php" class="nav-button">Status Serveur</a>
                <a href="https://github.com/sysmyks" class="nav-button">GitHub</a>
            </div>
        </div>
    </nav>
    <p></p>
    <div class="server-status">
        <div class="map-image" style="background-image: url('<?php echo $mapImageUrl; ?>')"></div>
        <div class="server-info">
        <h1 class="server-name"><?php echo $formattedStatus['hostname_colored']; ?></h1>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Carte</div>
                    <div class="info-value"><?php echo htmlspecialchars($formattedStatus['mapname']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Type de jeu</div>
                    <div class="info-value"><?php echo htmlspecialchars($formattedStatus['gametype']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Joueurs</div>
                    <div class="info-value"><?php echo htmlspecialchars($formattedStatus['players']); ?>/<?php echo htmlspecialchars($formattedStatus['maxplayers']); ?></div>
                </div>
                <?php if (!empty($mapInfo)): ?>
                <div class="info-item">
                    <div class="info-label">Auteur</div>
                    <div class="info-value"><?php echo htmlspecialchars($mapInfo['author'] ?? 'Inconnu'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Difficulté</div>
                    <div class="info-value"><?php echo htmlspecialchars($mapInfo['difficulty'] ?? '?'); ?>/100</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nombre de sauts</div>
                    <div class="info-value"><?php echo htmlspecialchars($mapInfo['jumps'] ?? '0'); ?></div>
                </div>
                <?php endif; ?>
                <!-- Remplacer la section des records dans l'info-grid -->
<?php if ($mapRecord && isset($mapRecord['best'])): ?>
    <?php foreach ($mapRecord['best'] as $way => $record): ?>
    <div class="info-item record">
        <div class="info-label">Record Way <?php echo htmlspecialchars($way); ?></div>
        <div class="info-value"><?php echo htmlspecialchars($record['time']); ?>s par <?php echo htmlspecialchars($record['player']); ?></div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
            </div>
            <?php if (!empty($formattedStatus['players_list'])): ?>
            <div class="players-list">
                <div class="info-label">Joueurs connectés (<?php echo count($formattedStatus['players_list']); ?>)</div>
                <?php foreach ($formattedStatus['players_list'] as $player): ?>
                <div class="player-item">
                    <span class="player-name"><?php echo $player['name']; ?></span>
                    <span class="player-ping"><?php echo $player['ping']; ?> ms</span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <!-- Remplacer la section "Tous les records" -->
<?php if ($mapRecord && isset($mapRecord['all'])): ?>
<div class="all-records">
    <h2>Tous les records de la map</h2>
    <?php foreach ($mapRecord['all'] as $way => $records): ?>
        <h3>Way <?php echo htmlspecialchars($way); ?></h3>
        <table class="records-table">
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Joueur</th>
                    <th>Temps</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $index => $record): ?>
                <tr <?php echo $index === 0 ? 'class="best-time"' : ''; ?>>
                    <td>#<?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($record['player']); ?></td>
                    <td><?php echo htmlspecialchars($record['time']); ?>s</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>
<?php endif; ?>
        </div>
    </div>
</body>
</html>
