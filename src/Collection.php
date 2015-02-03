<?php
	/**
	 * CzProject Innu DB
	 *
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */

	namespace Cz\InnuDb;
	use Nette;

	class Collection extends Nette\Object implements \Iterator
	{
		const ASC = 'ASC',
			DESC = 'DESC';

		/** @var  array */
		private $data;

		/** @var  int|NULL */
		private $limit;

		/** @var  int */
		private $offset = 0;

		/** @var  array|NULL  [column => sorting] */
		private $sorting = NULL;

		/** @var  array  [column => value] */
		private $conditions = array();

		/** @var  int|NULL */
		private $count;

		/** @var  array  @internal */
		private $filtredData;

		/** @var  int|string  @internal */
		private $iteratorKey;

		/** @var  array  [column => cmp => [values]] */
		private $formattedConditions;



		public function __construct(array $data)
		{
			$this->data = $data;
		}



		/**
		 * @return	array
		 */
		public function getData()
		{
			if($this->filtredData === NULL)
			{
				// where
				$data = $this->applyConditions($this->data);

				// limit & offset
				if(is_int($this->limit))
				{
					$data = array_slice($data, $this->offset, $this->limit, TRUE /*preserve keys*/);
				}

				// sorting
				$this->filtredData = $this->sorting($data);
			}

			return $this->filtredData;
		}



		/**
		 * @return	int
		 */
		public function getCount()
		{
			if($this->count === NULL)
			{
				$this->count = count($this->getData());
			}

			return $this->count;
		}



		/**
		 * @param	string|id
		 * @return	mixed|FALSE
		 */
		public function find($id)
		{
			$data = $this->getData();

			if(isset($data[$id]))
			{
				return $data[$id];
			}

			return FALSE;
		}



		public function fetch()
		{
			$item = $this->current();
			$this->next();
			return $item;
		}



		/**
		 * @param	int|NULL
		 * @param	int
		 * @return	Collection  fluent interface
		 */
		public function limit($limit, $offset = 0)
		{
			$this->reset();
			$this->limit = $limit !== NULL ? (int) $limit : NULL;
			$this->offset = (int) $offset;
			return $this;
		}



		/**
		 * @param	array  [column => ASC, column => DESC]
		 * @return	Collection
		 */
		public function sort($sorting, $sort = NULL)
		{
			$this->reset();
			if(!is_array($sorting))
			{
				$sorting = array(
					$sorting => $sort !== NULL ? $sort : 'ASC',
				);
			}

			if($this->sorting === NULL)
			{
				$this->sorting = $sorting;
			}
			else
			{
				$this->sorting = array_merge($this->sorting, $sorting);
			}

			return $this;
		}



		/**
		 * Alias for sort()
		 * @param	array  [column => ASC, column => DESC]
		 * @return	Collection
		 */
		public function order($sorting, $sort = NULL)
		{
			return $this->sort($sorting, $sort);
		}



		/**
		 * @param	array  [column => value, ...]
		 * @return	Collection
		 */
		public function where($column, $value = NULL)
		{
			$this->reset();
			if(!is_array($column))
			{
				$args = func_num_args();

				if($args === 1) // only column
				{
					throw new CollectionException("Missing parameter value, called only where('$column') without value");
				}

				$column = array(
					$column => $value,
				);
			}

			$this->conditions = array_merge($this->conditions, $column);
			return $this;
		}



		/**
		 * @param	array  [column => ASC, column => DESC]
		 * @return	array
		 */
		protected function sorting($data)
		{
			if($this->sorting === NULL)
			{
				return $data;
			}

			uasort($data, array($this, '_sortCmp'));
			return $data;
		}



		/**
		 * @param	mixed
		 * @param	mixed
		 * @return	int
		 */
		protected function _sortCmp($a, $b)
		{
			// -1   a < b
			//  0   a = b
			//  1   a > b

			foreach((array) $this->sorting as $column => $sorting)
			{
				if(!isset($a[$column]) || !isset($b[$column]))
				{
					throw new CollectionException("Sort: column '{$column}' is missing in first or second item.");
				}

				$sort = 0; // a = b

				if(is_string($a[$column]))
				{
					$sort = strcmp($a[$column], $b[$column]);
				}
				elseif($a[$column] < $b[$column])
				{
					$sort = -1;
				}
				elseif($a[$column] > $b[$column])
				{
					$sort = 1;
				}

				if($sort) // sort != 0
				{
					return ($sorting !== self::ASC) ? ($sort * -1) : $sort;
				}
			}

			return 0; // a = b, TODO: return -1 ? A < B ???
		}



		/**
		 * @return	array
		 */
		protected function applyConditions(array $data)
		{
			$this->formattedConditions = array();
			$hasConditions = FALSE;

			foreach($this->conditions as $cond => $value)
			{
				$hasConditions = TRUE;
				// >, <, =, <=, >=
				$column = $cond = trim($cond);
				$op = '=';
				$chars = substr($cond, -2, 2);
				if($chars === '>=' || $chars === '<=')
				{
					$op = $chars;
					$column = rtrim(substr($cond, 0, -2));
				}
				elseif($chars[1] === '=' || $chars[1] === '<' || $chars[1] === '>')
				{
					$op = $chars[1];
					$column = rtrim(substr($cond, 0, -1));
				}

				$this->formattedConditions[$column][$op][] = $value;
			}

			if(!$hasConditions)
			{
				return $data;
			}

			return array_filter($data, array($this, '_conditionCmp'));
		}



		/**
		 * @param	mixed
		 * @return	bool
		 */
		protected function _conditionCmp($item)
		{
			foreach($this->formattedConditions as $column => $ops)
			{
				if(!array_key_exists($column, $item))
				{
					throw new CollectionException("Where: column '$column' missing in item");
				}

				foreach($ops as $op => $values)
				{
					foreach($values as $value)
					{
						// TODO: value is callback -> return $callback($item, $op);
						// TODO: value is array -> item[column] in_array $value for =, etc.
						if($op === '=')
						{
							if($item[$column] !== $value)
							{
								return FALSE;
							}
						}
						elseif($op === '<')
						{
							if(!($item[$column] < $value))
							{
								return FALSE;
							}
						}
						elseif($op === '>')
						{
							if(!($item[$column] > $value))
							{
								return FALSE;
							}
						}
						elseif($op === '<=')
						{
							if(!($item[$column] <= $value))
							{
								return FALSE;
							}
						}
						elseif($op === '>=')
						{
							if(!($item[$column] >= $value))
							{
								return FALSE;
							}
						}
					}
				}
			}

			return TRUE;
		}



		/**
		 * Resets count & filtredData.
		 * @internal
		 * @return	void
		 */
		protected function reset()
		{
			$this->count = NULL;
			$this->filtredData = NULL;
		}



		/************************ Iterator methods **************************/
		public function rewind()
		{
			$this->getData();
			reset($this->filtredData);
			$this->iteratorKey = key($this->filtredData);
		}



		public function current()
		{
			$this->getData();
			$item = current($this->filtredData);
			$this->iteratorKey = key($this->filtredData);
			return $item;
		}



		public function key()
		{
			return key($this->filtredData);
		}



		public function next()
		{
			$this->getData();
			next($this->filtredData);
			$this->iteratorKey = key($this->filtredData);
		}



		public function valid()
		{
			$data = $this->getData();
			return isset($data[$this->iteratorKey]);
		}
	}



	class CollectionException extends \RuntimeException
	{
	}

