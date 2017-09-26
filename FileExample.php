<?php

// an abstract class acts as a template for children classes, it cannot be directly instantiated
abstract class FileAbstract
{
    // a private property or method can only be accessed by the class it was declared it, not its child classes
    private $file_name;
    // a protected property can be access by children classes, but not publicly
    protected $file_handle = null;

    // a public function or method can be accessed by any calling code
    // notice the type hint (type declaration) for $file_name is string
    public function __construct(string $file_name)
    {
        // assign the value passed into the constructor to an instance variable so we can refer to it in other methods
        $this->file_name = $file_name;
    }

    // a magic method that is called when the class is treated as a string
    function __toString()
    {
        $return_string = "File name: $this->file_name\n";
        if ($this->file_handle !== null) {
            $return_string .= "Lock type: " . $this->getLockType() . "\n";
            $return_string .= "File mode: " . $this->getFileMode() . "\n";
        }

        return $return_string;
    }

    // notice the return type hint for this method is string
    public function getFileName(): string
    {
        return $this->file_name;
    }

    // this method doesn't return anything, so its return type hint is void
    public function open(): void
    {
        // if the file is already open, then $this->file_handle will not be void
        if ($this->file_handle !== null) {
            return;
        }

        // use fopen to open the file using the mode given by the abstract class method getFileMode()
        $file_handle = fopen($this->file_name, $this->getFileMode());
        // if there is an error opening the file, throw an exception
        if ($file_handle === false) {
            throw new Exception("Failed to open file $this->file_name");
        }

        // save the file handle for later use as an instance variable
        $this->file_handle = $file_handle;

        // the lock method might throw an exception, so we need to put it in a try/catch block
        try
        {
            $this->lock();
        } catch (Exception $e) {
            // we are giving up on opening the file, so close it
            $this->close();
            // make sure we reset the file_handle to null so we know the file isn't currently open
            $this->file_handle = null;
            throw $e;
        }
    }

    private function lock(): void
    {
        // use flock to lock the file with the lock type given by the abstract class method getLockType()
        $lock_result = flock($this->file_handle, $this->getLockType());
        // if there is an error acquiring the lock, throw an exception
        if ($lock_result === false) {
            throw new Exception("Failed to acquire a lock of type " . $this->getLockType() . " on file $this->file_name");
        }
    }

    public function close(): void
    {
        // we can't close a file that isn't open
        if ($this->file_handle === null) {
            throw new Exception("Cannot close file $this->file_name because it is not currently open.");
        }

        // unlock the file
        flock($this->file_handle, LOCK_UN);
        // close the file
        fclose($this->file_handle);
        // set the file_handle to null so we know that the file isn't currently open
        $this->file_handle = null;
    }

    public function file_end_of_file(): bool
    {
        // use the feof function to determine if the file is at the end or not
        return feof($this->file_handle);
    }

    // if an instantiated class goes out of scope and is garbage collected by PHP without close being called first
    // then we should clean up by closing the file
    function __destruct()
    {
        if ($this->file_handle !== null) {
            $this->close();
        }
    }

    // children classes need to return the proper file mode
    public abstract function getFileMode(): string;

    // children classes need to return the proper lock type
    public abstract function getLockType(): int;
}

// FileReader is a concrete class that can be instantiated.
// It must implement all methods declared abstract in FileAbstract.
class FileReader extends FileAbstract {
    public function __construct(string $file_name)
    {
        // we need to make sure to call the parents constructor
        parent::__construct($file_name);
    }

    public function getFileMode(): string
    {
        return 'rb';
    }

    public function getLockType(): int
    {
        return LOCK_SH;
    }

    // since this class will be used to read from a file, provide a getLine() method to fetch a line from the file
    public function getLine(): string
    {
        // if the file isn't open, we can't read a line
        if ($this->file_handle === null) {
            throw new Exception("Cannot read frrom file " . $this->getFileName() . " because it is not currently open.");
        }

        // if we're at the end of the file, return an empty string
        if($this->file_end_of_file()) {
            return '';
        }

        // use fgets() to read from the file handle and trim off any white space such as a new line at the end of the string
        return trim(fgets($this->file_handle));
    }
}

// FileWriter is a concrete class that can be instantiated.
// It must implement all methods declared abstract in FileAbstract.
class FileWriter extends FileAbstract {
    public function __construct(string $file_name)
    {
        // we need to make sure to call the parents constructor
        parent::__construct($file_name);
    }

    public function getFileMode(): string
    {
        return 'wb';
    }

    public function getLockType(): int
    {
        return LOCK_EX;
    }

    // since this class will be used to write to a file, provide a writeString method to write a line to the file
    public function writeString(string $string_to_write): void {
        // if the file isn't open, we can't write a line
        if ($this->file_handle === null) {
            throw new Exception("Cannot write to file " . $this->getFileName() . " because it is not currently open.");
        }

        // use the fwrite function to write to the file handle
        $write_result = fwrite($this->file_handle, $string_to_write);
        // if the result is false there was an error, so throw an exception
        if ($write_result === false) {
            throw new Exception("Failed to write to file " . $this->getFileName() . "!");
        }
    }
}

// FileAppender is very similar to FileWriter.
// The only difference is the file mode used to open the file.
class FileAppender extends FileWriter
{
    public function __construct(string $file_name)
    {
        parent::__construct($file_name);
    }

    public function getFileMode(): string
    {
        return 'ab';
    }
}

// we'll use FileReader to read from the file read_test_file.txt
$my_file = new FileReader("read_test_file.txt");
$my_file->open();
// using $my_file with echo causes PHP to call the__toString() method automatically
echo $my_file . "\n";
while ($my_file->file_end_of_file() === false) {
    echo $my_file->getLine() . "\n";
}
$my_file->close();

// we'll use FileWriter to write to the file write_test_file.txt
$my_file = new FileWriter("write_test_file.txt");
$my_file->open();
echo $my_file . "\n";
$my_file->writeString("Testing here!\n");
$my_file->writeString("More tests!\n");
$my_file->close();

// we'll use FileAppender to append to the file write_test_file.txt
$my_file = new FileAppender("write_test_file.txt");
$my_file->open();
echo $my_file . "\n";
$my_file->writeString("Appending once!\n");
$my_file->writeString("Appending twice!\n");
$my_file->close();
