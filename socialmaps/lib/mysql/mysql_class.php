<?php
// mysql_class.php


// Class for performing operations on the database
class mysql
{	
	var $table;
	
	// Constructor that sets the table name
    function __construct($tablename)
	{
        $this->table = $tablename;
    }
	
	// Function to execute Raw SQL query that doesn't fits any other specific  functions
	function raw_query()
	{
		/** 
		*		This should have  2 arguments : 
		*		1st  argument should be the stuff before the table name.
		*    2nd argument should be the stuff after the table name.
		**/
		$arg_count = func_num_args();
		if($arg_count==2)
		{
			 $before = func_get_arg(0);
			 $after = func_get_arg(1);
			 $query = $before." ".$this->table." ".$after;
			 $this->execute($query);
		}
	}
	
	// Function to insert data into the table
	function insert()
	{
		$arg_count = func_num_args();
		if($arg_count==1)
		{
			$vlist = func_get_arg(0);
			$query = "INSERT INTO ".$this->table." VALUES (".$vlist.");";
			$this->execute($query);	
		}
		else if($arg_count==2)
		{
			$clist = func_get_arg(0);
			$vlist = func_get_arg(1);
			$query = "INSERT INTO ".$this->table." (".$clist.") VALUES (".$vlist.");";
			$this->execute($query);
		}
	}

	// Function to update a tuple in the table
	function update($clist,$vlist,$where)
	{
		$cols = explode(',',$clist);
		$vals = explode(',',$vlist);
		$c = count($cols);
		$v = count($vals);
		$query = "UPDATE ".$this->table." SET";
		for($i=0; $i<$c; $i++)
		{
			$query = $query." ".$cols[$i]."=".$vals[$i];
			if($i+1!=$c)
			$query = $query.",";
		}
		$query = $query." WHERE ".$where.";";
		$this->execute($query);
	}
	
	// Function to delete a tuple from the table
	function delete($where)
	{
		$query = "DELETE FROM ".$this->table." WHERE ".$where.";";
		$this->execute($query);
	}
	
	// Function to get records from the table
	function select()
	{
		$arg_count = func_num_args();
		if($arg_count==1)
		{
			$what = func_get_arg(0);
			$query = "SELECT ".$what." FROM ".$this->table.";";
			return $this->execute($query);
		}
		else if($arg_count==2)
		{
			$what = func_get_arg(0);
			$extra = func_get_arg(1);
			$query = "SELECT ".$what." FROM ".$this->table." ".$extra.";";
			return $this->execute($query);
		}
	}
	
	// (Private) Function to execute the framed SQL Query
	private function execute($query) 
	{
		global $con;
        $return_result = mysql_query($query, $con);
        if($return_result)
            return $return_result;
        else
            $this->mysql_error($query);
    }

	// (Private) Function to flash MySql Errors
    private function mysql_error($query)
	{
		global $con;
        echo mysql_error($con).'<br />';  // For Debugging Purposes Only
        die('error: '. $query);	    // Shouldn't be used due to Security Reason - Error shouldn't be displayed in any case
		echo "Database Error !!!";
    }
	
	// Function to add quotes to arguments
	function add_quotes($str)
	{
		$str = "'".$str."'";
		return $str;
	}
}
?>
