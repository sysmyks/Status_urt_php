<?php

class MapDataManager {
    private $mapInfoFile;
    private $recordsFile;

    public function __construct($mapInfoFile, $recordsFile) {
        $this->mapInfoFile = $mapInfoFile;
        $this->recordsFile = $recordsFile;
    }

    public function getMapInfo($mapName) {
        $mapInfo = [];
        
        if (file_exists($this->mapInfoFile)) {
            $mapData = json_decode(file_get_contents($this->mapInfoFile), true);
            if (isset($mapData[$mapName])) {
                $mapInfo = $mapData[$mapName];
                // Conversion de la difficulté en pourcentage si c'est un tableau
                if (is_array($mapInfo['difficulty'])) {
                    $mapInfo['difficulty'] = $mapInfo['difficulty'][0];
                }
            }
        }

        return $mapInfo;
    }

    public function getMapRecord($mapName) {
        if (file_exists($this->recordsFile)) {
            $recordsData = json_decode(file_get_contents($this->recordsFile), true);
            if (isset($recordsData[$mapName])) {
                $records = $recordsData[$mapName];
                $bestTime = PHP_FLOAT_MAX;
                $bestPlayer = '';
                
                foreach ($records as $player => $attempts) {
                    foreach ($attempts as $time) {
                        if ($time < $bestTime) {
                            $bestTime = $time;
                            $bestPlayer = $player;
                        }
                    }
                }
                
                // Conversion du temps
                $totalSeconds = $bestTime / 1000;
                $minutes = floor($totalSeconds / 60);
                $remainingSeconds = $totalSeconds - ($minutes * 60);
                
                // Formatage du temps dans le style M:SS.mmm ou SS.mmm
                if ($minutes > 0) {
                    // Pour les temps > 1 minute : M:SS.mmm
                    $timeFormatted = sprintf("%d:%06.3f", $minutes, $remainingSeconds);
                } else {
                    // Pour les temps < 1 minute : SS.mmm
                    $timeFormatted = sprintf("%06.3f", $remainingSeconds);
                }
                
                return [
                    'time' => $timeFormatted,
                    'player' => $bestPlayer,
                    'raw_time' => $bestTime
                ];
            }
        }
        return null;
    }
}