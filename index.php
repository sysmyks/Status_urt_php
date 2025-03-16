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
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/maps_styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/index.php" class="logo">LaFumisterie</a>
            <div class="nav-links">
                <a href="/index.php" class="nav-button">Dépôt</a>
                <a href="/status-v1/index.php" class="nav-button active">Status Serveur</a>
                <a href="/status-v1/maps.php" class="nav-button">Maps</a>
                <a href="https://github.com/sysmyks" class="nav-button">GitHub</a>
            </div>
        </div>
    </nav>
    
    <?php if (isset($formattedStatus['error'])): ?>
    <div class="server-error">
        <h2>Serveur inaccessible</h2>
        <p>Le serveur est actuellement indisponible. Veuillez réessayer plus tard.</p>
    </div>
    <?php else: ?>
    <div class="server-status">
        <div class="map-image" style="background-image: url('<?php echo $mapImageUrl; ?>')"></div>
        <div class="server-info">
        <h1 class="server-name"><?php echo $formattedStatus['hostname_colored']; ?></h1>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Carte</div>
                    <div class="info-value"><a href="https://lafumisterie.net/status-v1/map_details.php?map=<?php echo urlencode($formattedStatus['mapname']); ?>" class="download-link"><?php echo htmlspecialchars($formattedStatus['mapname']); ?></a></div>
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
                <?php if ($mapRecord && isset($mapRecord['best'])): ?>
                    <?php foreach ($mapRecord['best'] as $way => $record): ?>
                    <div class="info-item record">
                        <div class="info-label">Record Way <?php echo htmlspecialchars($way); ?></div>
                        <div class="info-value"><?php echo htmlspecialchars($record['time']); ?>s par <?php echo $record['player']; ?></div>
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
            
        </div>
    </div>
    <?php endif; ?>
</body>
</html>