<?php
	/** CzProject Innu DB
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Cz\InnuDb;
	
	class InnuDb
	{
		private $path;
		
		private $data;
		
		private $dataLoaded = FALSE;
		
		private $loader;
		
		
		
		public function __construct(Loader $loader, $path)
		{
			$this->loader = $loader;
			$this->path = realpath($path);
			
			if($this->path === FALSE)
			{
				throw new Exception('Data file not found in ' . $path);
			}
		}
		
		
		
		public function getPath()
		{
			return $this->path;
		}
		
		
		
		public function getData()
		{
			if(!$this->dataLoaded)
			{
				$this->data = (array) $this->loader->load($this->path);
				$this->dataLoaded = TRUE;
			}
			
			return $this->data;
		}
		
		
		
		public function createCollection($subset = NULL)
		{
			$data = $this->getData();
			
			if($subset !== NULL)
			{
				if(!isset($data[$subset]))
				{
					throw new \Exception("Collection: data subset '$subset' missing.");
				}
				
				$data = $data[$subset];
			}
			
			$collection = new Collection($data);
			
			return $collection;
		}
	}

