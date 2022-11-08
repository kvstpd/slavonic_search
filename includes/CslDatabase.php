<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.
    
include_once("includes/book_utils.php"); 

define('CSL_DB_RETURN_ARRAY_ASSOC', 1);
define('CSL_DB_RETURN_ARRAY_NUM', 2);
define('CSL_DB_RETURN_SINGLE_ROW_ASSOC', 3);
define('CSL_DB_RETURN_SINGLE_ROW_NUM', 4);
define('CSL_DB_RETURN_SINGLE_COLUMN', 5);
define('CSL_DB_RETURN_SINGLE_VALUE', 6);




abstract class CslDatabase
{
    abstract public function is_structure_in_place();
    
    abstract public function escape_string($string);
    
    abstract public function start_transaction();
    abstract public function commit_transaction();
    abstract public function rollback_transaction();
    
    abstract public function close();

    abstract public function select_query($query, $return_as);
   	abstract public function modify_query($query);

    
    abstract public function book_id_for_book_path($book_path);

    public function get_word_count()
    {
        return $this->select_query('SELECT count(word_id) FROM words', CSL_DB_RETURN_SINGLE_VALUE);
    }

    
    public function books_clause($books, $prepend_and = true)
    {
        if (empty($books))
            return '';
        
        $clause = $prepend_and ? ' AND ' : ' ';
        
        if (is_array($books))
            $clause .= 'occurences.book_id IN ('.implode(",", $books).') ';
        else if (is_string($books) && (strstr($books, ',') !== false) )
            $clause .= ' occurences.book_id IN ('.$books.') ';
        else
            $clause .= 'occurences.book_id='.intval($books).' ';
            
        return $clause;
    }
    
    public function dumb_books_clause($books)
    {
        if (empty($books))
            return '';
            
        return ' AND occurences.book_id IN ('.$books.') ';
    }
    
   public function search_string($str, $match_type, $limit, $is_simplified, $books = false)
	{
		$like_str = $str;

		if ($match_type == 'contains')
		{
			$like_str = '%'.$like_str.'%';
		}
		else if ($match_type == 'begins')
		{
			$like_str = $like_str.'%';
		}
		else if ($match_type == 'ends')
		{
			$like_str = '%'.$like_str;
		}
		
		$field = $is_simplified ? 'simplified' : 'word';

		$limit = intval($limit);
		
        $books_clause = $this->books_clause($books);
        
		$query = "SELECT word_id, MIN(word) as word, COUNT(position) as total FROM words INNER JOIN occurences USING (word_id) where $field LIKE '$like_str' $books_clause GROUP BY word_id ORDER BY total desc LIMIT $limit";

            //echo $query ;
       return $this->select_query($query,CSL_DB_RETURN_ARRAY_ASSOC);
    }
    
    
    public function word_occurences($word_id, $limit, $books = false)
    {
		$limit = intval($limit);
        $books_clause = $this->books_clause($books);
        
        return $this->select_query('SELECT occurence_id, name as book, path as path, position FROM occurences INNER JOIN books USING (book_id) where word_id='.$word_id.$books_clause.' ORDER BY book_id LIMIT '.$limit, CSL_DB_RETURN_ARRAY_ASSOC);
    }
    
    public function get_occurence($occurence_id)
    {
		$query =  'SELECT word, position, length, path FROM words INNER JOIN occurences USING (word_id) INNER JOIN books USING (book_id) where occurence_id='.$occurence_id;

        return $this->select_query($query, CSL_DB_RETURN_SINGLE_ROW_ASSOC);
    }

 	public function words_by_ids($word_ids)
    {
		$query =  'SELECT word_id, word, simplified FROM words where word_id in ('.implode(",", $word_ids).')';

        return $this->select_query($query, CSL_DB_RETURN_ARRAY_ASSOC);
    }

    public function words_by_size($only_count, $exact, $min, $max, $offset, $limit)
    {
		$query = $only_count ? 'select count(word_id) from words' : 'select word_id, word, simplified from words';

		// don't show numbers
		$query .= ' where (simplified + 0) = 0';

		if ($exact !== null)
		{
			$query .= " and length(simplified)=".intval($exact);
		}
		else
		{
			if ($min !== null)
			{
				$query .= " and length(simplified)>=".intval($min);
			}

			if ($max !== null)
			{
				$query .= " and	 length(simplified)<=".intval($max);
			}
		}
		
		if (!$only_count)
		{
			$offset = intval($offset);
			$limit = intval($limit);
			//$query .= ' order by random()';
			$query .= " order by word_id limit $limit offset $offset";
		}

        return $this->select_query($query, $only_count ? CSL_DB_RETURN_SINGLE_VALUE : CSL_DB_RETURN_ARRAY_ASSOC);
    }

	
	public function get_book_ids_of_types($types)
	{
		$type_clause = ' type IN ("'.implode('","', $types).'") ';

		return $this->select_query("SELECT book_id FROM books where $type_clause ORDER BY type, sort", CSL_DB_RETURN_SINGLE_COLUMN);
	}


    public function get_books()
    {
        return $this->select_query('SELECT book_id, type, path, description, name FROM books ORDER BY type, sort', CSL_DB_RETURN_ARRAY_ASSOC);
    }

    public function update_book_by_path($path, $name, $description)
    {
        return $this->modify_query('UPDATE books set name="'.$name.'", description="'.$description.'" where path="'.$path.'"');
    }

    public function insert_book_by_path($path)
    {
		$book_data = csl_default_book_data();

		if (isset($book_data[$path]) )
		{
            $sort = $book_data[$path][0];
			$name = $book_data[$path][1];
			$description = $book_data[$path][2];
            
            $type = isset($book_data[$path][3]) ? $book_data[$path][3] : 'c';

        	return $this->modify_query('INSERT INTO books (sort, path, type, description, name) VALUES ("'.$sort.'","'.$path.'", "'.$type.'",   "'.$description.'", "'.$name.'") ');
		}

		return false;
	}


    abstract public function insert_words($word_map, $file_path);
    
    abstract public function insert_words_for_slavoniser($word_map);

    
    abstract public function rebuild_structure();
    
    abstract public function rebuild_slavoniser_structure();
    
    abstract protected function prepare_for_word_insertion();
    abstract protected function cleanup_statements();
    abstract protected function fail_words_transaction($message);
    

    
    protected $link;
    protected $logger;
    
    protected $i_statement;
    protected $q_statement;
    protected $o_statement;
}

    
?>
