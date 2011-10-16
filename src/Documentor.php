<?php

namespace Faker;

class Documentor
{
	protected $generator;
	
	public function __construct($generator)
	{
		$this->generator = $generator;
	}
	
	public function getFormatters()
	{
		$formatters = array();
		foreach ($this->generator->getProviders() as $provider) {
			$providerClass = get_class($provider);
			$formatters[$providerClass] = array();
			$refl = new \ReflectionObject($provider);
			foreach ($refl->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflmethod) {
				$methodName = $reflmethod->name;
				if ($methodName == '__construct') {
					continue;
				}
				$parameters = array();
				foreach ($reflmethod->getParameters() as $reflparameter) {
					$parameter = '$'. $reflparameter->getName();
					if ($reflparameter->isDefaultValueAvailable()) {
						$parameter .= ' = ' . var_export($reflparameter->getDefaultValue(), true);
					}
					$parameters []= $parameter;
				}
				$parameters = $parameters ? '('. join(', ', $parameters) . ')' : '';
				$example = $this->generator->format($methodName);
				if (is_array($example)) {
					$example = "array('". join("', '", $example) . "')";
				} elseif($example instanceof \DateTime) {
					$example = $example->format('Y-m-d H:i:s');
				} elseif (is_string($example)) {
					$example = var_export($example, true);
				}
				$formatters[$providerClass][$methodName . $parameters] = $example;
			}
			ksort($formatters[$providerClass]);
		}
		ksort($formatters);
		
		return $formatters;
	}
	
}