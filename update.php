<?php

class Updater {
    /**
     * Where to store the Adblock file
     *
     * @var string
     */
    private $adblockFile = 'adblock-list.txt';

    /**
     * From where to get the Adblock file
     *
     * @var string
     */
    private $adblockUrl = 'https://www.zoso.ro/pages/rolist.txt';

    /**
     * Where to store the hosts file
     *
     * @var string
     */
    private $hostsFile = 'hosts';

    /**
     * Do it
     */
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

    /**
     * Generate hosts file content from an Adblock source
     *
     * @param  string $source Source contents
     *
     * @return string         Hosts contents
     */
    private function generateHosts($source) {
        $hosts = array();

        // Get only domains
        preg_match_all('/\n\|\|(.*)\^/', $source, $matches);

        // Keep unique values
        $matches = array_unique($matches[1]);

        // Sort
        asort($matches);

        // Build
        return '0.0.0.0 ' . implode("\n0.0.0.0 ", $matches);
    }

    /**
     * Return the contents of a file or an empty string if it doesn't exist
     *
     * @param  string $path File path
     *
     * @return string       File contents
     */
    private function loadFile($path) {
        if (file_exists($path)) {
            $contents = trim(file_get_contents($path));
        } else {
            $contents = '';
        }
        return $contents;
    }

    /**
     * Show a log message
     *
     * @param  string $message Message
     */
    private function log($message) {
        echo "[" . date('Y-m-d H:i:s') . '] ' . $message . "\n";
    }
}

new Updater;