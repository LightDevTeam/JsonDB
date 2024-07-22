<?php
class FastIO {
    private $handle;
    private $bufferSize;
    private $readBuffer;
    private $writeBuffer;
    private $writeBufferSize;

    public function __construct($filename, $mode, $bufferSize = 8192) {
        $this->handle = fopen($filename, $mode);
        if (!$this->handle) {
            throw new Exception("Could not open file: $filename");
        }
        $this->bufferSize = $bufferSize;
        $this->readBuffer = '';
        $this->writeBuffer = '';
        $this->writeBufferSize = 0;
    }

    public function readLine() {
        while (($pos = strpos($this->readBuffer, "\n")) === false) {
            $this->readBuffer .= fread($this->handle, $this->bufferSize);
            if (feof($this->handle)) break;
        }
        
        if (($pos = strpos($this->readBuffer, "\n")) !== false) {
            $line = substr($this->readBuffer, 0, $pos + 1);
            $this->readBuffer = substr($this->readBuffer, $pos + 1);
            return $line;
        } else {
            $line = $this->readBuffer;
            $this->readBuffer = '';
            return $line;
        }
    }

    public function write($data) {
        $this->writeBuffer .= $data;
        $this->writeBufferSize += strlen($data);
        if ($this->writeBufferSize >= $this->bufferSize) {
            $this->flush();
        }
    }

    public function flush() {
        if ($this->writeBufferSize > 0) {
            fwrite($this->handle, $this->writeBuffer, $this->writeBufferSize);
            $this->writeBuffer = '';
            $this->writeBufferSize = 0;
        }
    }

    public function __destruct() {
        $this->flush();
        fclose($this->handle);
    }
}