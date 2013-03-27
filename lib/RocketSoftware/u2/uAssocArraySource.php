<?php

namespace RocketSoftware\u2;

interface uAssocArraySource {
  public function fieldExists($field);
  public function get($delta);
}