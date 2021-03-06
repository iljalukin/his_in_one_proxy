<?php

include_once './libs/composer/vendor/autoload.php';

/**
 * Class GlobalTestSuite
 */
const PHPUNIT = true;
class GlobalTestSuite extends \PHPUnit\Framework\TestSuite
{
	const BLACKLIST = array(
		'Client.php',
		'GlobalTestSuite.php',
		'TestCaseExtension.php'
	);

	/**
	 * @return GlobalTestSuite
	 */
	public static function suite()
	{
		$unit_tests = \HisInOneProxy\Config\GlobalSettings::getInstance()->isPhpunitWithCoverage();
		\HisInOneProxy\Config\GlobalSettings::getInstance()->readCustomConfig('test/php_unit_config.json');
		\HisInOneProxy\Config\GlobalSettings::getInstance()->setPhpunitWithCoverage($unit_tests);
		$map = new \HisInOneProxy\DataModel\HisToEcsIdMapping(\HisInOneProxy\Config\GlobalSettings::getInstance()->returnConfig());
		$map->appendMapping("1",2);
		$map->appendMapping("2",3);
		$map->appendMapping("4",5);
		$map->appendMapping("232",55);
		$suite = new GlobalTestSuite();
		self::addTestSuiteFiles($suite);
		return $suite;
	}

	/**
	 * @param \PHPUnit\Framework\TestSuite $suite
	 */
	protected static function addTestSuiteFiles($suite)
	{

		$added		= array();
		$ignored	= array();

		$rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname(__FILE__)));
		foreach($rii as $file)
		{
			if($file->isFile() && $file->getExtension() === 'php')
			{
				$class = str_replace(array('class.', '.php'), '', $file->getBasename());
				if( in_array($file->getFilename(), self::BLACKLIST))
				{
					$ignored[] = $class;
				}
				else
				{
					require_once $file;
					$reflection = new \ReflectionClass($class);
					if(!$reflection->isAbstract())
					{
						$added[] = $class;
						$suite->addTestSuite($class);
					}
				}
			}
		}

		self::printStatus($added, $ignored);
	}

	/**
	 * @param $added
	 * @param $ignored
	 */
	protected static function printStatus($added, $ignored)
	{
		echo "Searching for TestSuites...\n\n";

		echo "Added " . count($added) . " files to TestSuite: \n";
		sort($added);
		foreach($added as $suite)
		{
			echo "\t added: $suite\n";
		}

		echo "Ignored " . count($ignored) . " files from TestSuite: \n";
		sort($ignored);
		foreach($ignored as $suite)
		{
			echo "\t ignored: $suite\n";
		}

		echo "\n...done. Running TestSuites now:\n\n";
	}
}
