<?php

	/*
	*	validation class
	*/

	class Validate
	{
		private $value;

		function Validate($val)
		{
			$this->value = $val;
		}

		public function isNumber()
		{
			$response = true;

			return $response;
		}

		public function isEmail()
		{
			$response = true;

			return $response;			
		}

		public function isString()
		{

			$response = true;

			return $response;
		}
	}

	/*
	*	data store handling,
	*	file dump based
	*/

	class Store
	{
		private $store;
		private $name;
		private $number;
		private $email;
		private $fileName;
		private $file;

		function Store()
		{
			//load data from file store
			$this->store = array();
			$this->fileName = "store.json";

			$this->getData();
		}

		public function getData()
		{
			if(file_exists($this->fileName))
			{
				/*
				*	read json ad transform to array
				*/

				$myFile = $this->fileName;
				$myFileLink = fopen($myFile, 'r') or die("Can't open file.");
				$myFileContents = fread($myFileLink, filesize($myFile));
				fclose($myFileLink);

				$this->store = json_decode($myFileContents, true)['data'];
			}

			return $this->store;
		}

		private function refreshFileStore()
		{
			//write data from store to file
			$newData = array('data' => $this->store, );
			$newContents = json_encode($newData, true);
			$myFile = $this->fileName;
			$myFileLink = fopen($myFile, 'w+') or die("Can't open file.");
			fwrite($myFileLink, $newContents);
			fclose($myFileLink);

		}

		public function removeData($name, $phone, $email)
		{
			$tmp = array(
				'message' => "SuccessFull Removal ",
				'dock' => "formEditDock",
				'alert' => "success" 
			);

			/*
			*	ops
			*/

			$found = false;

			foreach ($this->store as $key => $value)
			{
				$n = $value[0];
				$p = $value[1];
				$e = $value[2];

				if($n == $name and $p == $phone and $e == $email)
				{
					$this->store = array_diff($this->store, $value);
					$found = true;
				}
			}

			if($found == false)
			{
				$tmp['message'] = "Item Was not found in store";
				$tmp['alert'] = "danger";
			}

			$this->refreshFileStore();

			return $tmp;
		}

		public function insertData($name, $phone, $email)
		{
			$tmp = array(
				'message' => "SuccessFull Insert ",
				'dock' => "formMessage",
				'alert' => "success" 
			);

			/*
			*	ops
			*/

			$found = false;

			foreach ($this->store as $key => $value)
			{
				$n = $value[0];
				$p = $value[1];
				$e = $value[2];

				if($n == $name and $p == $phone and $e == $email)
				{
					$found = true;
				}
			}

			if($found == false)
			{
				array_push($this->store, [$name, $phone, $email]);
			}
			else
			{
				$tmp['message'] = "Dataset already in memory";
				$tmp['alert'] = "warning";
			}

			$this->refreshFileStore();

			return $tmp;
		}

	}

	session_start();

	$op = new Store();

	$data = json_decode(file_get_contents('php://input'), true);

	$check = array(
		"name" => "text",
		"phone" => "number",
		"email" => "email"
	);

	$load = array(
		'message' => "",
		'alert' => "warning" ,
		'data' => $op->getData()
	);

	$go = True;


	foreach ($check as $key => $type)
	{
		if(isset($data[$key]))
		{
			$value = $data[$key];
			$test = new Validate($value);

			if(strlen($value) > 0)
			{
				#text
				if($type == 'text')
				{
					if(!$test->isString())
					{
						$go = False;

						$tmp = array(
							'message' => "Invalid Input for ".$key,
							'dock' => $key."D",
							'alert' => "warning" 
						);

						$load[$key] = $tmp;
					}				
				}

				#email
				if($type == 'email')
				{
					if(!$test->isEmail())
					{
						$go = False;

						$tmp = array(
							'message' => "Invalid Input for ".$key,
							'dock' => $key."D",
							'alert' => "warning" 
						);

						$load[$key] = $tmp;
					}				
				}

				#number
				if($type == 'number')
				{
					if(!$test->isNumber())
					{
						$go = False;

						$tmp = array(
							'message' => "Invalid Input for ".$key,
							'dock' => $key."D",
							'alert' => "warning" 
						);

						$load[$key] = $tmp;
					}				
				}
			}
			else
			{
				$go = False;

				$tmp = array(
					'message' => "Provide input for ".$key,
					'dock' => $key."D",
					'alert' => "warning" 
				);

				$load[$key] = $tmp;
			}
		}
	}

	/*
	*	decision to proceed or terminate 
	*/

	if ($go == true)
	{
		if (isset($data['op']))
		{
			$op = new Store();
			$name = $data['name'];
			$phone = $data['phone'];
			$email = $data['email'];

			if ($data['op'] == 'save')
			{
				$msg = $op->insertData($name, $phone, $email);
				$load['global'] = $msg;
				$load['alert'] = $msg['alert'];
				$load['message'] = $msg['message'];
			}
			elseif($data['op'] == 'remove')
			{
				$msg = $op->removeData($name, $phone, $email);
				$load['global'] = $msg;
				$load['alert'] = $msg['alert'];
				$load['message'] = $msg['message'];
			}

			$load['data'] = $op->getData();
		}
	}
	else
	{
		$tmp = array(
			'message' => "Ensure all issues are resolved before saving ",
			'dock' => "formMessage",
			'alert' => "danger" 
		);

		$load['global'] = $tmp;
	}

	$response = array('data' => $load, );

	print json_encode($response);
