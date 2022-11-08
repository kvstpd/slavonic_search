<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.
    
include_once("CslLogger.php");
include_once("CslDatabase.php");

class CslDatabaseSqlite extends CslDatabase
{
    public function __construct($db_name, $logger)
    {
        $this->logger = $logger;
        
        $this->just_created = !file_exists($db_name);
        
		$flags = SQLITE3_OPEN_READONLY;

		if (ADMIN_MODE == 1)
        	$flags = $this->just_created ? (SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE) : SQLITE3_OPEN_READWRITE;


        $this->link = new SQLite3($db_name, $flags);
        
        if (empty($this->link))
        {
            throw new RuntimeException('sqlite object not created!');
        }
    }
    
    public function is_structure_in_place()
    {
        return !$this->just_created;
    }
    
    
    public function escape_string($string)
    {
        return SQLite3::escapeString($string);
    }
    
    
    public function start_transaction()
    {
        if (!$this->link->exec("BEGIN TRANSACTION"))
        {
            $this->logger->log(8, 'Unable to start transaction! Error: '.$this->link->lastErrorMsg());
            return false;
        }
    
        return true;
    }
    
    public function commit_transaction()
    {
        if (!$this->link->exec("COMMIT"))
        {
            $this->logger->log(8, 'Unable to commit transaction! Error: '.$this->link->lastErrorMsg());
            $this->rollback_transaction();
            return false;
        }
        
        return true;
    }
    
    
    public function rollback_transaction()
    {
        if (!$this->link->exec("ROLLBACK TRANSACTION"))
        {
            $this->logger->log(8, 'Unable to rollback transaction! Error: '.$this->link->lastErrorMsg());
            return false;
        }
        
        return true;
    }
    
    public function close()
    {
        $this->link->close();
    }




   	public function modify_query($query)
	{
		$result = $this->link->query($query);

       	if (!$result)
        {
            $this->logger->log(8, 'Query failed! Error: '.$this->link->lastErrorMsg());
            
            return false;
        }
        
		return true;
	}


   	public function select_query($query, $return_as)
	{
		$result = $this->link->query($query);

       	if (!$result)
        {
            $this->logger->log(8, 'Query failed! Error: '.$this->link->lastErrorMsg());
            
            return false;
        }
        else
        {
			$fetch_as =  SQLITE3_ASSOC;
			
			if (($return_as == CSL_DB_RETURN_ARRAY_NUM)
				|| ($return_as == CSL_DB_RETURN_SINGLE_ROW_NUM)
				|| ($return_as == CSL_DB_RETURN_SINGLE_COLUMN)
				|| ($return_as == CSL_DB_RETURN_SINGLE_VALUE) )
			{
				$fetch_as =  SQLITE3_NUM;
			}

             $q_result = array();
             
             while ($row = $result->fetchArray($fetch_as))
             {
				if ($return_as == CSL_DB_RETURN_SINGLE_VALUE)
					return $row[0];

				if (($return_as == CSL_DB_RETURN_SINGLE_ROW_NUM) || ($return_as == CSL_DB_RETURN_SINGLE_ROW_ASSOC))
					return $row;

                 $q_result[] =  ($return_as == CSL_DB_RETURN_SINGLE_COLUMN) ? $row[0] : $row;
             }
             
             return $q_result;
        }
	}

    
    public function book_id_for_book_path($book_path)
    {        
        $result = $this->link->query('SELECT book_id FROM books WHERE path="'.$book_path.'" LIMIT 1');
        
        if ($result)
        {
            $row = $result->fetchArray(SQLITE3_NUM);
            
            if (is_array($row) && (count($row) == 1) )
            {
                return $row[0];
            }
        }

		if ($this->insert_book_by_path($book_path))
        {
            $book_id =$this->link->lastInsertRowID();
            
            return $book_id;
        }
        
        return false;
    }
    
    
    public function insert_words_for_slavoniser($word_map)
    {
        if (!$this->start_transaction())
        {
            return false;
        }
        
        $sl_statement = $this->link->prepare('INSERT or FAIL INTO words (word, simplified, count) VALUES (?, ?, ?)');
        
        foreach ($word_map as $w => $word_data)
        {
            // first item is simplified word, remaining are positions in this book
            $sl_statement->bindValue(1, $w, SQLITE3_TEXT);
            $sl_statement->bindValue(2, $word_data[0], SQLITE3_TEXT);
            $sl_statement->bindValue(3, $word_data[1], SQLITE3_NUM);
    
            if (!$sl_statement->execute())
            {
                return $this->fail_words_transaction('Inserting into words table failed!');
            }
        }
        
        $sl_statement->close();
        

        return $this->commit_transaction();
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
            $this->logger->log(8, 'Unable to find or insert book data. Error: '.$this->link->lastErrorMsg());
            return false;
        }
        
        $this->prepare_for_word_insertion();
        
        foreach ($word_map as $w => $word_data)
        {
            // first item is simplified word, remaining are positions in this book
            $this->i_statement->bindValue(1, $w, SQLITE3_TEXT);
            $this->i_statement->bindValue(2, $word_data[0], SQLITE3_TEXT);
    
            if (!$this->i_statement->execute())
            {
                return $this->fail_words_transaction('Inserting into words table failed!');
            }
            
            $word_id = -1;
            
            if ($this->link->changes() == 1)
            {
                $word_id = $this->link->lastInsertRowID();
            }
            else
            {
                $this->q_statement->bindValue(1, $w, SQLITE3_TEXT);
                
                $result = $this->q_statement->execute();
                
                if ($result !== false)
                {
                    $row = $result->fetchArray(SQLITE3_NUM);
                    
                    if (count($row) == 1)
                    {
                        $word_id = $row[0];
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
            
            
            for ($i=1; $i < $npos; $i += 2 )
            {
                $this->o_statement->bindValue(1, $word_id, SQLITE3_INTEGER);
                $this->o_statement->bindValue(2, $book_id, SQLITE3_INTEGER);
                $this->o_statement->bindValue(3, $word_data[$i], SQLITE3_INTEGER);
                $this->o_statement->bindValue(4, $word_data[$i + 1], SQLITE3_INTEGER);
            
                if (!$this->o_statement->execute())
                {
                    return $this->fail_words_transaction('Unable to insert word occurences!');
                }
            }
        }
        
        $this->cleanup_statements();
        
        return $this->commit_transaction();
    }
    
 
    
    public function rebuild_slavoniser_structure()
    {
        if (!$this->link->exec("BEGIN TRANSACTION"))
        {
            $this->logger->log(9, 'Slavoniser data structure creation failed (1)! Error: '.$this->link->lastErrorMsg());
            return false;
        }
        
        $this->link->query('DROP TABLE IF EXISTS words');
        
        
        $this->link->exec('CREATE TABLE words (word_id INTEGER NOT NULL PRIMARY KEY, word TEXT NOT NULL, simplified TEXT NOT NULL, count INTEGER NOT NULL)');
       
        //$this->link->query('CREATE UNIQUE INDEX idx_word ON words (word)');

        $this->link->query('CREATE INDEX idx_simplified ON words (simplified)');

        
        
        if (!$this->link->exec("COMMIT"))
        {
            $this->logger->log(9, 'Slavoniser data structure creation failed (2)! Error: '.$this->link->lastErrorMsg());
            return false;
        }
        else
        {
            $this->logger->log(0, 'Slavoniser database structure successfully created.' );
        }
        
        return true;
    }
    
    
    public function rebuild_structure()
    {
        if (!$this->link->exec("BEGIN TRANSACTION"))
        {
            $this->logger->log(9, 'Data structure creation failed (1)! Error: '.$this->link->lastErrorMsg());
            return false;
        }
        
        $this->link->query('DROP TABLE IF EXISTS words');
        $this->link->query('DROP TABLE IF EXISTS books');
        $this->link->query('DROP TABLE IF EXISTS occurences');
        
        //$db->exec('CREATE TABLE letters (letter TEXT PRIMARY KEY, simplified TEXT) WITHOUT ROWID');
        $this->link->exec('CREATE TABLE words (word_id INTEGER NOT NULL PRIMARY KEY, word TEXT NOT NULL, simplified TEXT NOT NULL)');
        $this->link->exec('CREATE TABLE books (book_id INTEGER NOT NULL PRIMARY KEY, sort INTEGER NOT NULL, type TEXT NOT NULL, path TEXT NOT NULL, description TEXT NOT NULL, name TEXT NOT NULL)');
        $this->link->exec('CREATE TABLE occurences (occurence_id INTEGER NOT NULL PRIMARY KEY, word_id INTEGER NOT NULL, book_id INTEGER NOT NULL, position INTEGER NOT NULL, length INTEGER NOT NULL)');
        // , UNIQUE(word, path, position)
        $this->link->query('CREATE UNIQUE INDEX idx_word ON words (word)');
        $this->link->query('CREATE UNIQUE INDEX idx_book ON books (path)');
        //$link->query('CREATE INDEX idx_simplified ON words (simplified)');
        $this->link->query('CREATE INDEX idx_word_occurences ON occurences (word_id)');
        
        if (!$this->link->exec("COMMIT"))
        {
            $this->logger->log(9, 'Data structure creation failed (2)! Error: '.$this->link->lastErrorMsg());
            return false;
        }
        else
        {
            $this->logger->log(0, 'Database structure successfully created.' );
        }
        
        return true;
    }
    
    
    
    
    protected function prepare_for_word_insertion()
    {
        $this->i_statement = $this->link->prepare('INSERT or IGNORE INTO words (word, simplified) VALUES (?, ?)');
        $this->q_statement = $this->link->prepare('SELECT word_id FROM words WHERE word=?');
        $this->o_statement = $this->link->prepare('INSERT INTO occurences (word_id, book_id, position, length) VALUES (?, ?, ?, ?)');
    }
    
    protected function cleanup_statements()
    {
        $this->i_statement->close();
        $this->q_statement->close();
        $this->o_statement->close();
    }
    
    protected function fail_words_transaction($message)
    {
        $this->logger->log(8, $message.' Error: '.$this->link->lastErrorMsg());

        $this->rollback_transaction();
        $this->cleanup_statements();
        return false;
    }
    
    private $just_created;
}

?>
