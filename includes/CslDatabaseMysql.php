<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.
    
include_once("CslLogger.php");
include_once("CslDatabase.php");

    
class CslDatabaseMysql extends CslDatabase
{
    public function __construct($server, $db_name, $user, $password, $logger)
    {
        $this->logger = $logger;
        $this->link = new mysqli($server, $user, $password, $db_name);
        
        if (empty($this->link))
        {
            throw new RuntimeException('mysqli object not created!');
        }
        
        if ($this->link->connect_errno) {
            throw new RuntimeException('mysqli connection error: ' . $this->link->connect_error);
        }
    }
    
    public function is_structure_in_place()
    {
        if (!$this->link->query('SHOW CREATE TABLE books'))
        {
            return false;
        }
        
        return true;
    }
    
    
    public function escape_string($string)
    {
        return $this->link->real_escape_string($string);
    }
    
    public function start_transaction()
    {
        if (!$this->link->query('LOCK TABLES words WRITE, books WRITE, occurences WRITE'))
        {
            $this->logger->log(8, 'Unable to start transaction! Error: '.$this->link->error);
            return false;
        }
    
        return true;
    }
    
    
    public function commit_transaction()
    {
        if (!$this->link->query('UNLOCK TABLES') )
        {
            $this->logger->log(8, 'Unable to commit transaction! Error: '.$this->link->error);
            return false;
        }
        
        return true;
    }
    
    public function rollback_transaction()
    {
        if (!$this->link->query('UNLOCK TABLES') )
        {
            $this->logger->log(8, 'Unable to rollback transaction! Error: '.$this->link->error);
            return false;
        }
        
        return true;
    }
    
    public function close()
    {
        return $this->link->close();
    }
    

 	public function modify_query($query)
	{
		$result = $this->link->query($query);

       	if (!$result)
        {
            $this->logger->log(8, 'Query failed! Error: '.$this->link->error);
            
            return false;
        }

		return true;
	}


 	public function select_query($query, $return_as)
	{
		$result = $this->link->query($query);

       	if (!$result)
        {
            $this->logger->log(8, 'Query failed! Error: '.$this->link->error);
            
            return false;
        }
        else
        {
			$fetch_as =  MYSQLI_ASSOC;
			
			if (($return_as == CSL_DB_RETURN_ARRAY_NUM)
				|| ($return_as == CSL_DB_RETURN_SINGLE_ROW_NUM)
				|| ($return_as == CSL_DB_RETURN_SINGLE_COLUMN)
				|| ($return_as == CSL_DB_RETURN_SINGLE_VALUE) )
			{
				$fetch_as =  MYSQLI_NUM;
			}

             $q_result = array();
             
             while ($row = $result->fetch_array($fetch_as))
             {
				if ($return_as == CSL_DB_RETURN_SINGLE_VALUE)
					$row[0];

				if (($return_as == CSL_DB_RETURN_SINGLE_ROW_NUM) || ($return_as == CSL_DB_RETURN_SINGLE_ROW_ASSOC))
					return $row;

                 $q_result[] =  ($return_as == CSL_DB_RETURN_SINGLE_COLUMN) ? $row[0] : $row;
             }
             
             return $q_result;
        }
	}


    public function rebuild_slavoniser_structure()
    {
        // for now there's to reason to make this in MySql - makes things much more slow and complex than they should be
        return false;
    }

 
    
    public function rebuild_structure()
    {
        $this->link->query('DROP TABLE IF EXISTS words');
        $this->link->query('DROP TABLE IF EXISTS books');
        $this->link->query('DROP TABLE IF EXISTS occurences');
        
        //$db->query('CREATE TABLE letters (letter TEXT PRIMARY KEY, simplified TEXT) WITHOUT ROWID');
        $this->link->query('CREATE TABLE words (word_id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, word VARCHAR(64) NOT NULL, simplified VARCHAR(64) NOT NULL) ENGINE=MyISAM');
        $this->link->query('CREATE TABLE books (book_id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, sort TINYINT NOT NULL, type CHAR(1) NOT NULL, path VARCHAR(245) NOT NULL, description TEXT NOT NULL, name VARCHAR(64) NOT NULL) ENGINE=MyISAM');
        $this->link->query('CREATE TABLE occurences (occurence_id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, word_id MEDIUMINT UNSIGNED NOT NULL, book_id TINYINT UNSIGNED NOT NULL, position MEDIUMINT UNSIGNED NOT NULL, length TINYINT UNSIGNED NOT NULL) ENGINE=MyISAM');
        // , UNIQUE(word, path, position)
        $this->link->query('CREATE UNIQUE INDEX idx_word ON words (word)');
        $this->link->query('CREATE UNIQUE INDEX idx_book ON books (path)');
        //$link->query('CREATE INDEX idx_simplified ON words (simplified)');
        $this->link->query('CREATE INDEX idx_word_occurences ON occurences (word_id)');
        
        
        $this->logger->log(0, 'Database structure successfully created.' );
        
        
        return true;
    }
    
    
    public function book_id_for_book_path($book_path)
    {
        $result = $this->link->query('SELECT book_id FROM books where path="'.$book_path.'" LIMIT 1');
        
        if ($result)
        {
            $row = $result->fetch_array(MYSQLI_NUM);
            
            if (is_array($row) && (count($row) == 1) )
            {
                return $row[0];
            }
        }

        if ($this->insert_book_by_path($book_path))
        {
            return $this->link->insert_id;
        }
        
        return false;
    }
    
    public function insert_words_for_slavoniser($word_map)
    {
        // for now there's to reason to make this in MySql - makes things much more slow and complex than they should be
        return false;
    }
    
    
    public function insert_words($word_map, $book_path)
    {
        if (!$this->start_transaction())
        {
            return false;
        }
        
        $book_id = $this->book_id_for_book_path($book_path);
        
        if ($book_id === false)
        {
            $this->rollback_transaction();
            $this->logger->log(8, 'Unable to find or insert book data. Error: '.$this->link->error);
            return false;
        }
        
        $this->prepare_for_word_insertion();
        
        foreach ($word_map as $w => $word_data)
        {
            // first item is simplified word, remaining are positions in this book
            $this->i_statement->bind_param('ss', $w, $word_data[0]);
    
            if (!$this->i_statement->execute())
            {
                return $this->fail_words_transaction('Inserting into words table failed!');
            }
            
            $word_id = -1;
            
            if ($this->i_statement->affected_rows == 1)
            {
                $word_id = $this->link->insert_id;
            }
            else
            {
                $this->q_statement->bind_param('s', $w);
                
                if ($this->q_statement->execute())
                {
                    $result = $this->q_statement->get_result();
                    
                    if ($result)
                    {
                        $row = $result->fetch_array(MYSQLI_NUM);
                        
                        if (count($row) == 1)
                        {
                            $word_id = $row[0];
                        }
                    }
                }
            }
            
            if ($word_id == -1)
            {
                return $this->fail_words_transaction('Querying for word_id gave empty result!');
            }
            
            $npos = count($word_data);
            
            if ($npos <= 1)
            {
                return $this->fail_words_transaction('A word without any occurences happened!');
            }
            
            
            for ($i=1; $i < $npos; $i+=2 )
            {
                $this->o_statement->bind_param('iiii', $word_id, $book_id, $word_data[$i], $word_data[$i + 1]);
            
                if (!$this->o_statement->execute())
                {
                    return $this->fail_words_transaction('Unable to insert word occurences!');
                }
            }
        }
        
        $this->cleanup_statements();
        
        return $this->commit_transaction();
    }
    
    
    protected function fail_words_transaction($message)
    {
        $this->logger->log(8, $message.' Error: '.$this->link->error);

        $this->rollback_transaction();
        $this->cleanup_statements();
        return false;
    }
    
    
    protected function prepare_for_word_insertion()
    {
        $this->i_statement = $this->link->prepare('INSERT IGNORE INTO words (word, simplified) VALUES (?, ?)');
        $this->q_statement = $this->link->prepare('SELECT word_id FROM words WHERE word=?');
        $this->o_statement = $this->link->prepare('INSERT INTO occurences (word_id, book_id, position, length) VALUES (?, ?, ?, ?)');
    }
    
    protected function cleanup_statements()
    {
        $this->i_statement->close();
        $this->q_statement->close();
        $this->o_statement->close();
    }
}
?>
