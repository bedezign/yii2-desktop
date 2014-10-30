<?php
/**
 * This trait can be included and adds a function that will return all public properties and their values
 * from your class as an array.
 */

namespace bedezign\yii2\desktop\components;

trait PropertyExtractor
{
	/**
	 * Returns all public properties and their values for the current instance
	 * @param array   $extra      Extra properties to include on top
	 * @return array
	 */
	public function getPublicProperties($extra = null)
	{
		$class = new \ReflectionClass($this);
		$properties = [];
		foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property)
			if (!$property->isStatic()) {
				$property = $property->getName();
				$properties[$property] = $this->$property;
			}

		if (is_array($extra))
			foreach ($extra as $property)
				$properties[$property] = $this->$property;

		return $properties;
	}
}
