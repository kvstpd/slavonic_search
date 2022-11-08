<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.
    
class CslLogger
{
    private function __construct()
    {
        $this->startTime = hrtime(true);
    }
    
    public static function defaultLogger()
    {
        if (is_null(self::$sInstance))
        {
            self::$sInstance = new self();
        }
        
        return self::$sInstance;
    }
    
    public function log($errorLevel, $message)
    {
        $nEvents = count($this->events);
        $entry = array($errorLevel, $message,  (hrtime(true) - $this->startTime) / 1000000.0);
        
        $this->events[$nEvents] = $entry;
        
        if ($this->logImmediately)
        {
            $this->displayEntry($entry);
        }
    }
    
    public function setLogImmediately($imm)
    {
        $this->logImmediately = $imm;
    }
    
    public function setLogMode($mode)
    {
        $this->logMode = $mode;
    }
    
    public function printEntries($minLevel = 0, $maxLevel = PHP_INT_MAX)
    {
        $items = 0;
        
        foreach ($this->events as $entry)
        {
            if (($entry[0] >= $minLevel) && ($entry[0] <= $maxLevel))
            {
                if (($this->logMode == 'json') && ($items++ > 0) )
                    echo ', ';
                
                $this->displayEntry($entry);
            }
        }
    }
    
    
    private function displayEntry($logEntry)
    {
        if ($this->logMode == 'html')
        {
            $htmlClass = 'cslError'.$logEntry[0];
            echo "<p class='$htmlClass'>".$logEntry[1]." (time elapsed: ".$logEntry[2]." ms)</p>";
        }
        else if ($this->logMode == 'json')
        {
            echo '{ "level" : '.$logEntry[0].', "message" : "'.$logEntry[1].'", "elapsed" : "'.$logEntry[2].' ms"}';
        }
        else
        {
            echo $logEntry[1].' / '.$logEntry[0].' ('.$logEntry[2].' ms)\n';
        }
    }

	function fail_with_error_message($message)
	{
		$this->log(1, $message);
		
		if ($this->logMode == 'json')
		{
			echo '{ "success" : false, "result" : [], "errors" : [ ';
			$this->printEntries();
			echo ' ] }';
		}
		else
		{
			echo '<div class="CslLogger_Errors">';
			$this->printEntries();
			echo '</div>';
		}
		
		exit(1);
	}

    
    private $events = array();
    private $logImmediately = false;
    private $logMode = 'html';
    
    private $startTime;

    
    private static $sInstance = null;
}
    
?>
