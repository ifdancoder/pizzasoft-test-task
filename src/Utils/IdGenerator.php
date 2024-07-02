<?php

namespace App\Utils;

class IdGenerator {
    private $filePath = __DIR__ . '/../../storage/last_id.txt';
    private $timeout = 15;
    private $maxLength = 15;

    public function __construct($timeout = 5, $maxLength=15) {
        $this->$timeout = $timeout;
        $this->$maxLength = $maxLength;
    }

    private function incrementId($id) {
        $length = strlen($id);
        if ($length >= $this->maxLength) {
            throw new \Exception("Maximum ID length of {$this->maxLength} reached!");
        }

        for ($i = $length - 1; $i >= 0; $i--) {
            if ($id[$i] === 'z') {
                $id[$i] = 'a';
            } else {
                $id[$i] = chr(ord($id[$i]) + 1);
                break;
            }
        }

        if ($i < 0) {
            $id = 'a' . $id;
        }

        return $id;
    }

    public function getNextId() {
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, 'aaa');
            $nextId = 'aaa';
        }
        else {
            $file = fopen($this->filePath, 'c+');
            $startTime = time();
            while (!flock($file, LOCK_EX | LOCK_NB)) {
                if ((time() - $startTime) >= $this->timeout) {
                    fclose($file);
                    throw new \Exception("Unable to lock the file within {$this->timeout} seconds!");
                }
                usleep(100000);
            }
    
            $lastId = trim(fread($file, filesize($this->filePath)));
            $nextId = $this->incrementId($lastId);
            ftruncate($file, 0);
            rewind($file);
            fwrite($file, $nextId);
            flock($file, LOCK_UN);
            fclose($file);
        }
        
        return $nextId;
    }
}