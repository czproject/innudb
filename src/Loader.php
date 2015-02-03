<?php
	/**
	 * CzProject Innu DB
	 *
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */

	namespace Cz\InnuDb;
	use Nette;

	class Loader
	{
		protected $adapters = array(
			'php' => 'Nette\Config\Adapters\PhpAdapter',
			'ini' => 'Nette\Config\Adapters\IniAdapter',
			'neon' => 'Nette\Config\Adapters\NeonAdapter',
			'json' => 'Cz\InnuDb\Adapters\JsonAdapter',
		);



		/**
		 * Inspired by Nette Framework
		 * @param	string
		 * @return	mixed
		 */
		public function load($file)
		{
			if(!is_file($file) || !is_readable($file))
			{
				throw new Nette\FileNotFoundException("File '$file' is missing or is not readable.");
			}

			return $this->getAdapter($file)->load($file);
		}



		/**
		 * Method from Nette Framework
		 * @author	David Grudl, Nette Foundation, Nette Community
		 * @param	string
		 * @param	mixed
		 * @return	void
		 */
		public function save($file, $data)
		{
			if(file_put_contents($file, $this->getAdapter($file)->dump($data)) === FALSE)
			{
				throw new Nette\IOException("Cannot write file '$file'.");
			}
		}



		/**
		 * Method from Nette Framework
		 * @author	David Grudl, Nette Foundation, Nette Community
		 * @param	string
		 * @param	string|Nette\Config\IAdapter
		 * @return	Loader  provides a fluent interface
		 */
		public function addAdapter($extension, $adapter)
		{
			$this->adapters[strtolower($extension)] = $adapter;
			return $this;
		}



		/**
		 * Method from Nette Framework
		 * @author	David Grudl, Nette Foundation, Nette Community
		 * @param	string
		 * @return	Nette\Config\IAdapter
		 */
		private function getAdapter($file)
		{
			$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

			if(!isset($this->adapters[$extension]))
			{
				throw new Nette\InvalidArgumentException("Unknown file extension '$file'.");
			}

			return is_object($this->adapters[$extension]) ? $this->adapters[$extension] : new $this->adapters[$extension];
		}
	}

