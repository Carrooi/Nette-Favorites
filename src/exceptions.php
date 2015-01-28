<?php

namespace Carrooi\Favorites;

class RuntimeException extends \RuntimeException {}

class InvalidArgumentException extends \InvalidArgumentException {}

class AssetsNamespaceNotExists extends RuntimeException {}

class AssetsResourceNotExists extends RuntimeException {}

class InvalidStateException extends RuntimeException {}

class ItemAlreadyInFavorites extends InvalidStateException {}

class ItemNotInFavorites extends InvalidStateException {}
