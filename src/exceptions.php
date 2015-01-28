<?php

namespace Carrooi\Favorites;

class RuntimeException extends \RuntimeException {}

class InvalidArgumentException extends \InvalidArgumentException {}

class InvalidStateException extends RuntimeException {}

class ItemAlreadyInFavorites extends InvalidStateException {}

class ItemNotInFavorites extends InvalidStateException {}
