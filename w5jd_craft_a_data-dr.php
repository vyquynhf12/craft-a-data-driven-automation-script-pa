<?php

class ScriptParser {
  private $script;
  private $variables;
  private $actions;

  public function __construct($script) {
    $this->script = $script;
    $this->variables = array();
    $this->actions = array();
  }

  public function parse() {
    $lines = explode("\n", $this->script);
    foreach ($lines as $line) {
      $line = trim($line);
      if (empty($line)) continue;
      $parts = explode(":", $line, 2);
      $type = trim($parts[0]);
      $value = trim($parts[1]);

      switch ($type) {
        case 'var':
          $var_parts = explode("=", $value, 2);
          $var_name = trim($var_parts[0]);
          $var_value = trim($var_parts[1]);
          $this->variables[$var_name] = $var_value;
          break;
        case 'action':
          $action_parts = explode("(", $value, 2);
          $action_name = trim($action_parts[0]);
          $action_params = trim($action_parts[1], ")");
          $this->actions[] = array('name' => $action_name, 'params' => $action_params);
          break;
        default:
          throw new Exception("Unknown script type: $type");
      }
    }
  }

  public function getVariables() {
    return $this->variables;
  }

  public function getActions() {
    return $this->actions;
  }
}

class AutomationScript {
  private $parser;

  public function __construct($script) {
    $this->parser = new ScriptParser($script);
    $this->parser->parse();
  }

  public function getVariables() {
    return $this->parser->getVariables();
  }

  public function getActions() {
    return $this->parser->getActions();
  }

  public function execute() {
    $actions = $this->parser->getActions();
    foreach ($actions as $action) {
      switch ($action['name']) {
        case 'send_email':
          $this->sendEmail($action['params']);
          break;
        case 'create_file':
          $this->createFile($action['params']);
          break;
        default:
          throw new Exception("Unknown action: {$action['name']}");
      }
    }
  }

  private function sendEmail($params) {
    // send email implementation
  }

  private function createFile($params) {
    // create file implementation
  }
}

// Example usage:
$script = "
var: name = John Doe
var: email = john@example.com
action: send_email(email)
action: create_file(john.txt)
";

$automation = new AutomationScript($script);
$automation->execute();

?>