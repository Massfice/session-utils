<?php

  namespace Massfice\SessionUtils;

  use Massfice\Storage\ShelfBuilder;
  use Massfice\Storage\ShelfSessionLoader;

  class SessionUtils {

    public static function advancedStore(
      string $shelf_name,
      string $key,
      $data,
      bool $override_allowed = true,
      bool $session_override_allowed = true
    ) {

      $shelf_name = sha1($shelf_name);
      $shelf = ShelfBuilder::getBuilder()
        ->setJsonAllowed(true)
        ->setSessionAllowed(true)
        ->setOverrideSessionAllowed(true)
        ->load($shelf_name);

      $shelf->addData($key,$data,$override_allowed);
      $shelf->storeSession($shelf_name,$session_override_allowed);

    }

    public static function store(string $key, $data) {
      self::advancedStore('simple_store_shelf',$key,$data);
    }

    public static function advancedLoad(
      string $shelf_name,
      string $key,
      bool $keep = true
    ) {

      $r = null;
      $shelf_name = sha1($shelf_name);

      if(ShelfSessionLoader::isStored($shelf_name)) {
        $shelf = ShelfSessionLoader::load($shelf_name);
        $r = $shelf->getData($key);
        if(!$keep) {
          $shelf->clearData($key);
          $shelf->storeSession($shelf_name,true);
        }
      }

      return $r;
    }

    public static function load(string $key, bool $keep = true) {
      return self::advancedLoad('simple_store_shelf',$key,$keep);
    }

    public static function advancedUnset(
      string $shelf_name,
      string $key
    ) {
      self::advancedLoad($shelf_name,$key,false);
    }

    public static function unset(string $key) {
      self::advancedUnset('simple_store_shelf',$key);
    }

    public static function removeShelf(string $shelf_name) {
      $shelf_name = sha1($shelf_name);
      ShelfSessionLoader::remove($shelf_name);
    }

    public static function getJson(string $shelf_name) : string {
      if(ShelfSessionLoader::isStored($shelf_name)) return ShelfSessionLoader::load($shelf_name)->makeJson();
      else return '';
    }

  }
?>
