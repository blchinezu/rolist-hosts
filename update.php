<?php

class Updater {
    private $adblockFile = 'adblock-list.txt';

    private $adblockUrl = 'https://www.zoso.ro/pages/rolist.txt';

    private $hostsFile = 'hosts';

    public function __construct() {

        $this->log('Get Adblock file from online');
        $onlineContents = trim(file_get_contents($this->adblockUrl));

        if (empty($onlineContents)) {
            $this->log('Adblock source is empty!');
            return;
        }

        $this->log('Load old adblock file');
        $offlineContents = $this->loadFile($this->adblockFile);

        if ($onlineContents != $offlineContents) {
            $this->log('Load old hosts');
            $oldHosts = $this->loadFile($this->hostsFile);

            $this->log('Generate new hosts');
            $newHosts = $this->generateHosts($onlineContents);

            if ($oldHosts != $newHosts) {
                $this->log('Write new hosts');
                file_put_contents($this->hostsFile, $newHosts);
                file_put_contents($this->adblockFile, $onlineContents); # should move it upper
                $this->log('Done');
            }
        } else {
            $this->log('Adblock source is unchanged.');
            return;
        }
    }

    private function generateHosts($source) {
        $hosts = array();

        preg_match_all('/\|\|(.*)\^/', $source, $matches);

        $matches = array_unique($matches[1]);

        print_r($matches);
    }

    private function loadFile($path) {
        if (file_exists($path)) {
            $contents = trim(file_get_contents($path));
        } else {
            $contents = '';
        }
        return $contents;
    }

    private function log($message) {
        echo "[" . date('Y-m-d H:i:s') . '] ' . $message . "\n";
    }
}

new Updater;