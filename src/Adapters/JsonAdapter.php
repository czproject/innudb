<?php
	/** CzProject Innu DB
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Cz\InnuDb\Adapters;
	use Nette,
		Nette\Utils\Json;
	
	class JsonAdapter implements Nette\Config\IAdapter
	{
		/**
		 * @param	string
		 * @return	mixed
		 */
		public function load($file)
		{
			return Json::decode(file_get_contents($file));
		}
		
		
		
		/**
		 * @param	array
		 * @return	void
		 */
		public function dump(array $data)
		{
			return Json::encode($data);
		}
	}

