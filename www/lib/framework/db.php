<?php
		if (!$str)
		{
			if($str != 0 && $str != 0.0 && $str != "0")
			{
				return "'".str_replace("'", "''", $str)."'";
			else
			{
				if ($str == "")
				{
					if ($allowEmptyString)
						return "'".str_replace("'", "''", $str)."'";
			}
			return "NULL";			
		}
		{