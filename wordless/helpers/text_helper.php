<?php

class Cycle {

  function __construct($values) {
    $this->values = $values;
    $this->index = 0;
  }

  function reset() {
    $this->index = 0;
  }

  function current_value() {
    return $this->values[$this->index];
  }

  function value() {
    $value = $this->current_value();
    $this->next();
    return $value;
  }

  function next() {
    $this->index = ($this->index + 1) % count($this->values);
  }
}

class TextHelper {

  private static $cycles = array();

  function pluralize($string)
  {

    $plural = array(
      array( '/(quiz)$/i',               "$1zes"   ),
      array( '/^(ox)$/i',                "$1en"    ),
      array( '/([m|l])ouse$/i',          "$1ice"   ),
      array( '/(matr|vert|ind)ix|ex$/i', "$1ices"  ),
      array( '/(x|ch|ss|sh)$/i',         "$1es"    ),
      array( '/([^aeiouy]|qu)y$/i',      "$1ies"   ),
      array( '/([^aeiouy]|qu)ies$/i',    "$1y"     ),
      array( '/(hive)$/i',               "$1s"     ),
      array( '/(?:([^f])fe|([lr])f)$/i', "$1$2ves" ),
      array( '/sis$/i',                  "ses"     ),
      array( '/([ti])um$/i',             "$1a"     ),
      array( '/(buffal|tomat)o$/i',      "$1oes"   ),
      array( '/(bu)s$/i',                "$1ses"   ),
      array( '/(alias|status)$/i',       "$1es"    ),
      array( '/(octop|vir)us$/i',        "$1i"     ),
      array( '/(ax|test)is$/i',          "$1es"    ),
      array( '/s$/i',                    "s"       ),
      array( '/$/',                      "s"       )
      );

    $irregular = array(
      array( 'move',   'moves'    ),
      array( 'sex',    'sexes'    ),
      array( 'child',  'children' ),
      array( 'man',    'men'      ),
      array( 'person', 'people'   )
      );

    $uncountable = array(
      'sheep',
      'fish',
      'series',
      'species',
      'money',
      'rice',
      'information',
      'equipment'
      );

    // save some time in the case that singular and plural are the same
    if ( in_array( strtolower( $string ), $uncountable ) )
      return $string;

    // check for irregular singular forms
    foreach ( $irregular as $noun )
    {
      if ( strtolower( $string ) == $noun[0] )
        return $noun[1];
    }

    // check for matches using regular expressions
    foreach ( $plural as $pattern )
    {
      if ( preg_match( $pattern[0], $string ) )
        return preg_replace( $pattern[0], $pattern[1], $string );
    }

    return $string;
  }


  function cycle() {
    $values = func_get_args();
    if (is_array($values[count($values)-1])) {
      $options = array_pop($values);
    }
    $name = isset($options["name"]) ? $options["name"] : "default";

    if (!isset(self::$cycles[$name])) {
      self::$cycles[$name] = new Cycle($values);
    }

    $cycle = self::$cycles[$name];
    return $cycle->value();
  }

  function truncate($text, $options = array()) {
    $options = array_merge(
      array(
        'length' => 30,
        'omission' => '...',
        'separator' => false
      ),
      $options
    );

    $length_with_room_for_omission = $options['length'] - strlen($options['omission']);
    if ($options['separator']) {
      $stop = strrpos($text, $options['separator'], min(strlen($text), $length_with_room_for_omission));
      if ($stop === FALSE) {
        $stop = $length_with_room_for_omission;
      }
    } else {
      $stop = $length_with_room_for_omission;
    }

    if (strlen($text) > $options['length']) {
      return substr($text, 0, $stop) . $options['omission'];
    } else {
      return $text;
    }
  }

}

Wordless::register_helper("TextHelper");
