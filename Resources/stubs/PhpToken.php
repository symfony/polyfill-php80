<?php

if (\PHP_VERSION_ID < 80000) {
  class PhpToken
  {
      /**
       * @var int
       */
      public $id;

      /**
       * @var string
       */
      public $text;

      /**
       * @var int
       */
      public $line;

      /**
       * @var int
       */
      public $pos;

      /**
       * @param int|string $id
       * @param string $text
       * @param int $line
       * @param int $position
       */
      public function __construct($id, $text, $line = -1, $position = -1)
      {
          $this->id = is_int($id) ? $id : ord($id);
          $this->text = $text;
          $this->line = $line;
          $this->pos = $position;
      }

      /**
       * @return string|null
       */
      public function getTokenName()
      {
          $name = token_name($this->id);
          return $name === 'UNKNOWN' ? null : $this->name;
      }

      /**
       * @param int|string|array $kind
       * @return bool
       */
      public function is($kind)
      {
          $token = (is_int($kind) || (is_array($kind) && is_int(current($kind))))
              ? $this->id
              : $this->getTokenName();
          return in_array($token, (array) $kind, true);
      }

      /**
       * @return bool
       */
      public function isIgnorable()
      {
          return $this->is([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT, T_INLINE_HTML]);
      }

      /**
       * @return string
       */
      public function __toString()
      {
          return (string) $this->text;
      }

      /**
       * @param string $code
       * @param int $flags
       * @return static[]
       */
      public static function tokenize($code, $flags = 0)
      {
          $tokens = [];
          $line = 1;
          $position = 0;
          foreach (token_get_all($code, $flags) as $token) {
              if (is_string($token)) {
                  $id = $text = $token;
              } else {
                  [$id, $text, $line] = $token;
              }
              $tokens[] = new static($id, $text, $line, $position);
              $position += strlen($text);
          }
          return $tokens;
      }
  }
}
