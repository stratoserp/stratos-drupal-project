<?php

declare(strict_types=1);

namespace StratosERP\Robo\Plugin\Commands;

/**
 * Testing commands Robo extension.
 */
trait TestingCommands {

  /**
   * Run the phpunit test suite.
   *
   * @aliases test
   *
   * @return bool
   *   Return success or not.
   */
  public function testAll(): bool {
    if (!$this->checkInstallState()) {
      return FALSE;
    }

    $this->prepareForTests();

    $command = $this->setPhpUnitCommand();

    return $this->_exec($command)->wasSuccessful();
  }

  /**
   * Run the phpunit test suite.
   *
   * @param string $group
   *   The group of tests to run.
   * @param string $filter
   *   Filter tests to the function level.
   *
   * @aliases test-group
   *
   * @return bool
   *   Return success or not.
   */
  public function testGroup(string $group = '', string $filter = ''): bool {
    if (!$this->checkInstallState()) {
      return FALSE;
    }

    $this->prepareForTests();

    $command = $this->setPhpUnitCommand($filter);

    return $this->_exec("$command --group $group")->wasSuccessful();
  }

  /**
   * List available test groups.
   *
   * @return bool
   *   Return success or not.
   */
  public function testGroups(): bool {
    if (!$this->checkInstallState()) {
      return FALSE;
    }

    return $this->_exec("phpunit --list-groups")->wasSuccessful();
  }

  /**
   * Cleanup browser output.
   */
  public function testClean(): void {
    $this->_exec('rm -f web/sites/simpletest/browser_output/*');
  }

  /**
   * Check that Drupal is installed.
   */
  private function checkInstallState(): bool {
    if (getenv('SQLITE_DATABASE')) {
      $dbCheck = $this->drush('sql:query')
        ->arg(".tables 'node'")
        ->printOutput(FALSE)
        ->run();
    }
    else {
      $dbCheck = $this->drush('sql:query')
        ->arg("show tables like 'node'")
        ->printOutput(FALSE)
        ->run();
    }

    if (trim($dbCheck->getMessage()) !== 'node') {
      $this->say("Drupal not installed, execute robo build.\n");
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Perform some housekeeping before running tests.
   */
  private function prepareForTests(): void {
    $this->devCacheRebuild();

    // Ensure example data module installed for testing.
    $this->drush('pm:enable')
      ->arg('se_example_data')
      ->option('yes')
      ->run();

    $this->_exec('mkdir -p web/sites/simpletest/browser_output');
    $this->_cleanDir('web/sites/simpletest/browser_output/');
  }

  /**
   * Set up the full phpunit command line.
   *
   * @param string $filter
   *   Filter tests to the function level.
   *
   * @return string
   *   The combined command string.
   */
  private function setPhpUnitCommand(string $filter = ''): string {

    $vars = $flags = [];

    $flags[] = '--stop-on-fail';
    $flags[] = '--testdox';

    if ($filter) {
      $flags[] = '--filter=' . $filter;
    }

    return implode(" \\\n", $vars) . ' phpunit ' . implode(' ', $flags);
  }

}
