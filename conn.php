<?php
	require('conf.php');

	/*
	*	interface to the database
	*/

	class Bridge
	{
		private $conn;
		private $connectFail;

		function Bridge()
		{
			$this->connectFail = False;
			$this->conn = mysqli_connect(HOST, USER, PASS, DBASE) or $this->connectFail = True;

			if ($this->connectFail == True)
			{
				/*
				*	connection failure
				*/

				print ('connection failed, Check the connection params'.PHP_EOL);
				die();
			}

			/*
			*	implement database tables
			*/

			$this->generateTables();			
		}

		/*
		*	mysqli connection instance 
		*/

		public function getConnection()
		{
			if ($this->connectFail == False)
			{
				return $this->conn;
			}
		}

		/*
		*	interface to mysqli
		*/

		public function execQuery($query="")
		{
			if (strlen($query) > 0)
			{
				$er = False;
				$fetch = mysqli_query($this->conn, $query) or $er = mysqli_error($this->conn);

				if ($er == False)
				{
					return $fetch;
				}
				else
				{
					return 0;
				}
			}
			else
			{
				return 0;
			}
		}

		/*
		*	pack readable respone from database
		*/

		public function packData($query="")
		{
			if (strlen($query) > 0)
			{
				$data = $this->execQuery($query);
				$buffer = array();

				while($look = mysqli_fetch_array($data))
				{
					array_push($buffer, $look);
				}

				return $buffer;
			}
		}

		/*
		*	table generation for the database
		*/

		private function generateTables()
		{
			if ($this->connectFail == False)
			{
				/*
				*	database schema access
				*/

				$content = file_get_contents('dbase.sql');
				$queries = explode(";\n\n", $content);

				foreach ($queries as $key => $query)
				{
					$er = False;
					$df = $this->execQuery($query);

					#print $df."\n".$er.PHP_EOL;
				}
			}
		}
	}

	/*
	*	directory manager and inference
	*/

	class FileDirectoryMap
	{
		private $bridge;
		private $contentTypes;
		private $contentTypeQuery;
		private $Content;
		private $contentQuery;

		function FileDirectoryMap()
		{
			$this->bridge = new Bridge();
			$this->contentTypeQuery = "SELECT * FROM contentType";
			$this->contentQuery = "SELECT * FROM content";

			/*
			*	load type of content
			*/

			$this->loadContentTypes();
		}

		/*
		*	list of contentTypes
		*/

		private function loadContentTypes()
		{
			$query = $this->contentTypeQuery;
			$list = $this->bridge->packData($query);
			$this->contentTypes = $list;
			
			/*
			*	generate new contentTypes [Folder, File]

			*/

			if (count($this->contentTypes) == 0)
			{
				/*
				*	INSERT INTO contentType VALUES (null, 'Folder', null)
				*	INSERT INTO contentType VALUES (null, 'File', null)
				*/

				$qrs = array();
				array_push($qrs, ["'Folder'", "'_'"]);
				array_push($qrs, ["'File'", "'_'"]);

				$ts = $this->logContentType($qrs);
				#var_dump($ts);
			}
			else
			{
				$r= 1;
				#var_dump($this->contentTypes);
				#$arrayName = array(0 => ["'Folderr'", "null"], );
				#$this->logContentType($arrayName);
			}			
		}

		/*
		*	new Content Type logging
		*/

		private function logContentType($load = array())
		{
			$ret = array();
			if (count($load) > 0)
			{
				foreach ($load as $key => $value)
				{
					$name = $value[0];
					$ext = $value[1];
					$loaded = False;

					/*
					*	check values not in contentTypes
					*/

					foreach ($this->contentTypes as $keyy => $valuee)
					{
						#print $valuee['contentTypeName'].' '.$name.PHP_EOL;
						
						$tmpName = "'".$valuee['contentTypeName']."'";
						$tmpExt = "'".$valuee['extension']."'";
						if (($valuee['contentTypeName'] == $name or $tmpName == $name) and ($valuee['extension'] == $ext or $tmpExt == $ext))
						{
							$loaded = True;
							array_push($ret, $valuee['id']);
						}
					}

					if($loaded == False)
					{
						$qry = "INSERT INTO contentType VALUES(null, $name, $ext)";
						#print $qry.PHP_EOL;
						$this->bridge->execQuery($qry);
					}

				}
			}
			else
			{
				print "Ensure you provide arguments for processing";
			}

			/*
			*	refresh the list
			*/

			$query = $this->contentTypeQuery;
			$this->contentTypes=$this->bridge->packData($query);

			#print count($ret).' '.count($load).PHP_EOL;

			if (count($ret) == count($load))
			{
				return $ret;
			}
			else
			{
				$ret = $this->logContentType($load);
				return $ret;
			}
		}

		/*
		*	loading content i.e. File and Folders
		*	null, contentName, container, contentTypeID
		*	INSERT INTO content VALUES(null, name, container, typeID)
		*/

		public function logContent($load=array())
		{
			/*
			*	load content from db
			*/

			$query = $this->contentQuery;
			$this->content = $this->bridge->packData($query);

			if (count($load) > 0)
			{
				foreach ($load as $key => $value)
				{
					$value = str_replace("\\", "/", $value);
					$paths = explode('/', $value);
					$sections = count($paths);
					$idMap = array();
					$container = 0;
					$cCounter = 0;
					$contentTypeID = 0;
					#var_dump($paths);

					/*
					*	check values not in content
					*	null, contentName, container, contentTypeID
					*/

					
					foreach($paths as $keyy => $valuee)
					{
						$loaded = False;
						#print $valuee.PHP_EOL;

						$contentName = $valuee;

						/*
						*	determine type of content
						*/

						$segs = explode('.', $contentName);
						$mods = $arrayName = array('0' => ["'Folder'", "'_'"], );

						if (count($segs) > 1)
						{
							//file
							//looping could gurantee access to names like this.this.jpg
							$contentName = $segs[0];
							$ext = "'".$segs[count($segs)-1]."'";
							$mods = $arrayName = array('0' => ["'File'", $ext], );
						}

						#var_dump($mods);
						$cTypeID = $this->logContentType($mods)[0];						
						
						foreach ($this->content as $keyyy => $valueee)
						{
							#var_dump($valueee);
							#print $keyy.' '.$keyyy.PHP_EOL;
							$cName = $valueee['contentName'];
							$cID = $valueee['id'];
							$cContainer = $valueee['contentContainer'];
							$cType = $valueee['contentTypeID'];

							if($cName == $contentName)
							{	
								if ($keyy == 0 and $cContainer == 0)
								{
									$container = $cID;
									$contentTypeID = $cType;
									$loaded = True;
									$cCounter++;
									break;
								}
								elseif($cCounter == $keyy and $cContainer == $container)
								{
									$cCounter++;
									$container = $cID;
									$contentTypeID = $cType;
									$loaded = True;
									break;
								}
							}

							//die();
						}

						if($loaded == False)
						{
							$contentName = "'$contentName'";

							$qry = "INSERT INTO content VALUES(null, $contentName, $container, $cTypeID)";
							#print $qry.PHP_EOL;
							$this->bridge->execQuery($qry);

							/*
							*	load content from db
							*/

							$query = $this->contentQuery;
							$this->content = $this->bridge->packData($query);

							/*
							*	recall function
							*/

							$this->logContent($load);
							break;
						}
					}
				}
			}
			else
			{
				print "Ensure you provide arguments for processing";
			}
		}

		/*
		*	load filesystem
		*/

		private function loadFilesSystem()
		{
			/*
			*	preping for content search
			**/
			$query = "SELECT c.id, c.contentName, c.contentContainer, ct.extension FROM content c, contentType ct WHERE c.contentTypeID = ct.id";
			$this->content = $this->bridge->packData($query);
		}

		/*
		*	search file structure
		*/

		public function searchContent($param="")
		{
			$paths = array();

			if (strlen($param) > 1)
			{
				/*
				*	prep search space
				*/

				$this->loadFilesSystem();

				/*
				*	receing params and making sure its not sql injectable
				*/

				$param = strtolower(mysqli_escape_string($this->bridge->getConnection(), $param));

				$query = "SELECT c.id, c.contentName, c.contentContainer, ct.extension FROM content c, contentType ct WHERE c.contentTypeID = ct.id AND (c.contentName LIKE '%$param%' OR ct.extension LIKE '%$param%')";
				$payLoad = $this->bridge->packData($query);
				#var_dump($payLoad);
				
				foreach ($payLoad as $key => $value)
				{
					$id = $value['id'];
					$name = $value['contentName'];
					$container = $value['contentContainer'];
					$extension = $value['extension'];

					/*
					*	adding extension for files
					*/

					if ($extension != "_")
					{
						$name = $name.".$extension";
					}

					/*
					*	constracting path
					*/

					$paths[$id] = $name;

					/*
					*	backtrack
					*/
					$term = False;

					while($term == False)
					{
						foreach ($this->content as $keyy => $valuee)
						{
							$idTmp = $valuee['id'];
							$nameTmp = $valuee['contentName'];
							$containerTmp = $valuee['contentContainer'];
							$extensionTmp = $valuee['extension'];

							if ($idTmp == $container)
							{
								$id = $value['id'];
								$name = $nameTmp;
								$container = $containerTmp;
								$extension = $extensionTmp;

								if ($containerTmp == 0)
								{
									$term = True;
								}

								if (isset($paths[$id]))
								{
									$paths[$id] = "$name\\".$paths[$id];
								}

								break;
							}
							
						}
					}

					#var_dump($paths);
				}
			}

			return $paths;
		}

	}

/*
*	mysqli_query(conn, query) or mysqli_error(conn)
*	
*/
?>