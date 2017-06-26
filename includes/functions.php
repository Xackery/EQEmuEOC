<?php

    function mysqli_result($result, $row, $field=0) {         
        $result->data_seek($row); 
        $datarow = $result->fetch_array(); 
        return $datarow[$field]; 
    }

    function mysqli_field_name($result, $field_offset)
    {
        $properties = mysqli_fetch_field_direct($result, $field_offset);
        return is_object($properties) ? $properties->name : null;
    }

	function PageTitle($PT){
		global $db, $App_Title;  
		echo '<script type="text/javascript">document.title = "[' . $App_Title . '] ' . $PT . '"; </script>';
	}

	function FormStart(){
        return '<div class="portlet-body form form-horizontal form-bordered form-row-stripped" >';
    }
	function FormEnd(){
        return '</div>';
    }
	function FormInput($Label, $Input){ 
		return '<div class="form-group">
			<label class="col-md-2 control-label">' . $Label . '</label>
			<div class="col-md-6"> <div class="input-group"> ' . $Input . ' </div> </div>
		</div>'; 
	}
	/* HREF Initiatior Example:
		<a class="btn blue ajax-modal" modalurl="global.php?spellview=' . $FieldData .  '">View</a>
	*/
	function Modal($Title, $Content, $Buttons){ 
		return '<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
			<h3 class="modal-title">' . $Title . '</h3>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					' . $Content . '
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn default" data-dismiss="modal">Close</button>
			' . $Buttons .'
		</div>';
	}
	function HoverTip($url){
		return " hovertip-url='" . $url . "' hovertip-hidemouseout='1' ";
	}
	function HoverCloseTip($url){
		return " hovertip-close-url='" . $url . "' hovertip-hidemouseout='1' ";
	}
	
	function SectionHeader($data, $desc = ''){
		if($desc != ''){ $a_desc = '<br><small>' . $desc . '</small>'; }
		return '<div class="alert alert-warning alert-dismissable"><h4 style="display:inline;font-weight:bold">' . $data . '</h4>'. $a_desc . '</div>';
	}

	/* global $db, Scoped Functions */

    function p_var_dump($data){
        print '<pre>';
        print var_dump($data);
        print '</pre>';
    }

    function DuplicateMySQLRecord ($table, $id_field, $id_copied_from, $copied_to_id = 0) {
        /* load the original record into an array */

        $result = mysqli_query($db, "SELECT * FROM {$table} WHERE {$id_field} = {$id_copied_from}");
        # echo mysqli_error();
        $original_record = mysqli_fetch_assoc($result);
        if ($copied_to_id == 0) {
            /* insert the new record and get the new auto_increment id */
            mysqli_query($db, "INSERT INTO {$table} (`{$id_field}`) VALUES (NULL)");
            $new_id = mysqli_insert_id();
            echo mysqli_error();
        }
        else {
            $new_id = $copied_to_id;
            mysqli_query($db, "INSERT INTO {$table} (`{$id_field}`) VALUES (" . $copied_to_id . ")");
        }

        /* generate the query to update the new record with the previous values */
        $query = "UPDATE {$table} SET ";
        foreach ($original_record as $key => $value) {
            if ($key != $id_field) {
                $query .= '`' . $key . '` = "' . str_replace('"', '\"', $value) . '", ';
            }
        }
        $query = substr($query, 0, strlen($query) - 2); // lop off the extra trailing comma
        $query .= " WHERE {$id_field}={$new_id}";
        mysqli_query($db, $query);

        # echo mysqli_error();

        return $new_id;
    }

    function GetNextAvailableIDInTable($table_name, $id_field){
        $query = '
            SELECT
            t1.' . $id_field . ' + 1 AS next_id
            FROM ' . $table_name . ' t1
            LEFT JOIN ' . $table_name . ' t2
            ON t1.' . $id_field . ' + 1 = t2.' . $id_field . '
            WHERE t2.' . $id_field . ' IS NULL
            ORDER BY next_id DESC
            LIMIT 1';
        $result = mysqli_query($db, $query);echo mysqli_error();
        while($row = mysqli_fetch_array($result)){
            return $row['next_id'];
        }
    }

?>