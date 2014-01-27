<?php
	/** CzProject Innu DB
	 *
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */

	namespace Cz\InnuDb;

	class InnuDb
	{
		/** @var  string */
		private $path;

		/** @var  array */
		private $data;

		/** @var  bool @internal */
		private $dataLoaded = FALSE;

		/** @var  Loader */
		private $loader;



		/**
		 * @param	Loader
		 * @param	string
		 * @throws	InnuDbException
		 */
		public function __construct(Loader $loader, $path)
		{
			$this->loader = $loader;
			$this->path = realpath($path);

			if($this->path === FALSE)
			{
				throw new InnuDbException("Data file not found in $path");
			}
		}



		/**
		 * @return	string
		 */
		public function getPath()
		{
			return $this->path;
		}



		/**
		 * @return	mixed
		 */
		public function getData()
		{
			if(!$this->dataLoaded)
			{
				$this->data = (array) $this->loader->load($this->path);
				$this->dataLoaded = TRUE;
			}

			return $this->data;
		}



		/**
		 * @param	string|NULL
		 * @return	Collection
		 * @throws	InnuDbException
		 */
		public function createCollection($subset = NULL)
		{
			$data = $this->getData();

			if($subset !== NULL)
			{
				if(!isset($data[$subset]))
				{
					throw new InnuDbException("Collection: data subset '$subset' missing.");
				}

				$data = $data[$subset];
			}

			$collection = new Collection($data);

			return $collection;
		}
	}



	class InnuDbException extends \RuntimeException
	{
	}

