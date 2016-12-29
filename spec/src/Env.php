<?php

namespace LostKobrakai\ProcessWire\Kahlan;

use Kahlan\Suite;
use Kahlan\Cli\Kahlan;
use ProcessWire\Config;
use Kahlan\Filter\Filter;

class Env
{
	/** @var string path to root folder of processwire */
	private static $root;

	/** @var Env Singleton instance of this class */
	private static $instance;

	public static function bootstrap(Kahlan $kahlan, $root = null)
	{
		self::$root = $root ?: realpath(__DIR__.'/../../../../');
		$env = self::$instance = new self;

		Filter::register('processwire.start', function ($chain) use ($env, $kahlan) {
			$kahlan->suite()->given('processwire', $env->createInstance());
			$kahlan->suite()->given('pw', $env->createInstance(false));
			return $chain->next();
		});

		require_once __DIR__ . '/helpers.php';
		Filter::apply($kahlan, 'interceptor', 'processwire.start');
	}

	public function createInstance($custom_db = true)
	{
		return function() use($custom_db) {
			var_dump('starting processwire');

			return (object)['custom_db' => $custom_db];
		};
	}


	public static function wrap($wrappers, $closure, $mode = 'normal')
	{
		$befores = $afters = [];
		$wrappers = (array) $wrappers;
		$env = self::$instance;

		foreach ($wrappers as $wrapper) {
			list($before, $after) = $env->getWrappingFunctions($wrapper);
			$befores[] = $before;
			$afters[] = $after;
		}

		$message = 'Following specs run using: ' . implode(', ', $wrappers) . ' ⤵';

		$context = Suite::current()->context($message, $closure, null, $mode);
		
		foreach (array_filter($befores) as $callback) {
			$context->beforeEach($callback);
		}

		foreach (array_reverse(array_filter($afters)) as $callback) {
			$context->afterEach($callback);
		}

		return $context;
	}

	public function getWrappingFunctions($for)
	{
		$before = $after = null;

		switch (strtolower($for)) {
			case 'migration':
			case 'migrations':
				$before = function() {
					Suite::current()->processwire;
					var_dump('running migrations');
					//Suite::current()->processwire->wire('modules')->get('Migrations')->migrate('*');
				};
				break;

			case 'transaction':
			case 'transactions':
				$before = function () {
					$suite = Suite::current();
					Suite::current()->processwire;
					// if($suite->processwire->wire('config')->dbEngine !== 'InnoDB'){
					// 	throw new \RuntimeException('Cannot use transactions on MyISAM tables.');
					// }
					// $suite->processwire->wire('database')->beginTransaction();
					var_dump('running transaction');
					//Suite::current()->processwire->wire('modules')->get('Migrations')->migrate('*');
				};
				$after = function () {
					$suite = Suite::current();
					Suite::current()->processwire;
					// $suite->processwire->wire('database')->rollBack();
					var_dump('rollback');
				};
				break;
		}

		return [$before, $after];
	}
}